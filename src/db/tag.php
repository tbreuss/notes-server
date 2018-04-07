<?php

namespace db\tag;

use DB;

function find_all(string $sort): array
{
    $orders = [
        'name' => 'name ASC',
        'frequency' => 'frequency DESC, name ASC',
        'changed' => 'modified DESC, name ASC',
        'created' => 'created DESC, name ASC',
        'default' => 'name ASC'
    ];
    $order = $orders[$sort] ?? $orders['default'];

    $sql = "
        SELECT id, name, frequency
        FROM tags
        ORDER BY {$order};
    ";

    $tags = DB::query($sql)->fetchAll();
    return $tags;
}

function find_one(int $id): array
{
    $sql = "
        SELECT *
        FROM tags
        WHERE id = :id;
    ";
    $tag = DB::query($sql, ['id' => $id])->fetch();
    return empty($tag) ? [] : $tag;
}

function update_all(array $oldTags, array $newTags, array $user)
{
    $tagsToRemove = array_udiff($oldTags, $newTags, "strcasecmp");
    $tagsToAdd = array_udiff($newTags, $oldTags, "strcasecmp");

    foreach ($tagsToRemove as $tag) {
        $sql = "
            UPDATE tags
            SET 
                frequency = frequency -1,
                modified = NOW(),
                modified_by = :modified_by
            WHERE name = :name;              
        ";
        $params = [
            'modified_by' => $user['id'],
            'name' => $tag
        ];
        DB::exec($sql, $params);
    }

    foreach ($tagsToAdd as $tag) {
        save_one($tag, $user);
    }

    delete_all_unused();

    if (empty($newTags)) {
        return [];
    }

    $sql = "SELECT id FROM tags WHERE name IN (:names);";
    $ids = DB::query($sql, ['names' => $newTags])
        ->fetchAll(DB::instance()::FETCH_COLUMN);

    return $ids;
}

function delete_all_unused()
{
    $sql = "
        DELETE FROM tags
        WHERE frequency <= 0;
    ";
    $rowCount = DB::exec($sql);
    return $rowCount;
}

function save_all(array $tags, int $userId): array
{
    $ids = [];
    foreach ($tags as $tag) {
        $ids[] = save_one($tag, $userId);
    }
    return $ids;
}

function save_one(string $tag, int $userId): int
{
    $sql = "
        SELECT id
        FROM tags
        WHERE name = :name;
    ";
    $id = (int)DB::query($sql, ['name' => $tag])->fetchColumn();

    if ($id > 0) {
        /*
        $sql = "
            UPDATE tags
            SET
              frequency = frequency + 1,
              modified = NOW(),
              modified_by = :modified_by
            WHERE id = :id;
        ";
        DB::exec($sql, [
            'id' => $id,
            'modified_by' => $user['id']
        ]);
        */
        return $id;
    } else {
        $sql = "
            INSERT INTO tags (name, frequency, created, created_by)
            VALUE (:name, 1, NOW(), :created_by);
        ";
        DB::exec($sql, [
            'name' => $tag,
            'created_by' => $userId
        ]);
        return DB::lastInsertId();
    }
}

function find_selected_tags(string $q, array $tags): array
{
    $sql = '
		SELECT t.id, t.name, count(a.id) AS count
		FROM tags t
		INNER JOIN articles a ON FIND_IN_SET(t.id, a.tag_ids)>0 
		WHERE 1=1
	';

    $params = [];

    if (!empty($q)) {
        $q = '%' . $q . '%';
        $sql .= ' AND (a.title LIKE ? OR a.content LIKE ?)';
        $params[] = $q;
        $params[] = $q;
    }

    if (!empty($tags)) {
        foreach ($tags as $tag) {
            $sql .= ' AND FIND_IN_SET(?, a.tag_ids)>0';
            $params[] = $tag;
        }
    }

    $sql .= '
		GROUP BY t.id
		ORDER BY count DESC, t.name ASC
		LIMIT 40
	';

    $stmt = DB::prepare($sql);
    $stmt->execute($params);
    $tags = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    //sort($tags);
    return $tags;
}

function update_frequencies(array $ids = [])
{
    $sql = "
        UPDATE tags
        SET frequency = (
        SELECT COUNT(tag_id)
            FROM article_to_tag
            WHERE tags.id = article_to_tag.tag_id
            GROUP BY tag_id
        )
    ";
    if (!empty($ids)) {
        $sql .= " WHERE id IN (:ids)";
    }
    return DB::exec($sql, ['ids' => $ids]);
}
