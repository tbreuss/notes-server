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

    $ids = DB::query("SELECT id FROM tags WHERE name = :name;", ['name' => $newTags])
        ->fetchAll(DB::instance()::FETCH_COLUMN);

    return $ids;
}

function find_ids(array $newTags)
{

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

function save_all(array $tags, array $user): array
{
    $ids = [];
    foreach ($tags as $tag) {
        $ids[] = save_one($tag, $user);
    }
    return $ids;
}

function save_one(string $tag, array $user): int
{
    $sql = "
        SELECT id
        FROM tags
        WHERE name = :name;
    ";
    $id = (int)DB::query($sql, ['name' => $tag])->fetch();

    if ($id > 0) {
        $sql = "
            UPDATE tags
            SET
              frequency = frequency + 1,
              modified = NOW(),
              modified_by = :modified_by
            WHERE id = :id;
        ";
        DB::exec($sql, ['id' => $id]);
        return $id;
    } else {
        $sql = "
            INSERT INTO tags (name, frequency, created, created_by)
            VALUE (:name, 1, NOW(), :created_by);
        ";
        DB::exec($sql, [
            'name' => $tag,
            'created_by' => $user['id']
        ]);
        return DB::lastInsertId();
    }
}

function find_selected_tags(string $q, array $tags): array
{
    $sql = '
		SELECT t.name, count(a.id) AS frequency
		FROM tags t
		INNER JOIN articles a ON FIND_IN_SET(t.name, a.tags)>0 
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
            $sql .= ' AND FIND_IN_SET(?, a.tags)>0';
            $params[] = $tag;
        }
    }

    $sql .= '
		GROUP BY t.name
		ORDER BY frequency DESC
		LIMIT 40
	';

    $stmt = DB::prepare($sql);
    $stmt->execute($params);
    $tags = $stmt->fetchAll(\PDO::FETCH_COLUMN);

    sort($tags);
    return $tags;
}
