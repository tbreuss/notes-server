<?php

namespace db\article;

use function common\array_iunique;
use DB;
use db\article_to_tag;
use db\article_views;
use db\tag;
use db\user;

function find_popular()
{
    $sql = "
        SELECT id, title, views
        FROM articles 
        ORDER BY views DESC
        LIMIT 5;
    ";
    $articles = DB::query($sql)->fetchAll();
    return $articles;
}

function find_latest()
{
    $sql = "
        SELECT id, title, created
        FROM articles 
        ORDER BY created DESC
        LIMIT 5;
    ";
    $articles = DB::query($sql)->fetchAll();
    return $articles;
}

function find_modified()
{
    $sql = "
        SELECT id, title, modified
        FROM articles 
        ORDER BY modified DESC
        LIMIT 5;
    ";
    $articles = DB::query($sql)->fetchAll();
    return $articles;
}

function find_liked()
{
    $sql = "
        SELECT id, title, likes
        FROM articles 
        ORDER BY likes DESC
        LIMIT 5;
    ";
    $articles = DB::query($sql)->fetchAll();
    return $articles;
}

/**
 * @return array
 * @deprecated
 */
function find_all_tags()
{
    $columns = DB::query("SELECT tags FROM articles;")->fetchAll(DB::instance()::FETCH_COLUMN);
    $tags = [];
    foreach ($columns as $strTags) {
        $arrTags = explode(',', $strTags);
        $tags = array_merge($tags, $arrTags);
    }
    $tags = array_iunique($tags);
    sort($tags);
    return $tags;
}

function find_one(int $id, bool $throwException = true): array
{
    $sql = "
        SELECT a.*, GROUP_CONCAT(t.name) AS tags
        FROM articles a
        INNER JOIN tags t ON FIND_IN_SET(t.id, a.tag_ids)
        WHERE a.id = :id;
    ";
    $article = DB::query($sql, ['id' => $id])->fetch();
    if ($throwException && empty($article)) {
        throw new \Exception('Not found');
    }
    $ids = [$article['created_by'], $article['modified_by']];
    $users = user\find_by_user_ids($ids);
    $article['created_by_user'] = $users[$article['created_by']] ?? [];
    $article['modified_by_user'] = $users[$article['modified_by']] ?? [];
    $article['views_date'] = article_views\find_latest_date($id);
    $article['tags'] = explode(',', $article['tags']);
    return $article;
}

function increase_views(int $id, int $userId)
{
    $sql = "
        SELECT COUNT(*) AS count
        FROM article_views
        WHERE article_id = :article_id
        AND user_id = :user_id
        AND created = :created;
    ";

    $params = [
        'article_id' => $id,
        'user_id' => $userId,
        'created' => date('Y-m-d')
    ];

    $count = DB::query($sql, $params)->fetchColumn();
    if (empty($count)) {

        $sql = "INSERT INTO article_views VALUES (:article_id, :user_id, :created);";
        DB::exec($sql, $params);

        $sql = "UPDATE articles SET views = views + 1 WHERE id = :id;";
        DB::exec($sql, ['id' => $id]);
    }

}

function save_tags(string $tags, int $articleId, int $userId, array $oldTagIds)
{
    // Doubletten entfernen
    $tags = explode_tags($tags);

    // Tags in Tabelle speichern
    $tagIds = tag\save_all($tags, $userId);

    // Tag-IDs in Zwischentabelle speichern
    article_to_tag\save_tags($articleId, $tagIds);

    // Tag-IDs in Artikel aktualisieren
    update_tag_ids($tagIds, $articleId);

    // Counter in Tags aktualisieren
    tag\update_frequencies(array_merge($tagIds, $oldTagIds));

    // Tags mit Counter=0 entfernen
    tag\delete_all_unused();
}

function insert(array $data, int $userId): int
{
    $tags = $data['tags'];
    unset($data['tags']);

    $sql = "
        INSERT INTO articles (title, content, created, created_by)
        VALUES (:title, :content, NOW(), :created_by);
    ";

    $data['created_by'] = $userId;

    DB::exec($sql, $data);

    $id = DB::lastInsertId();

    save_tags($tags, $id, $userId, []);

    return $id;
}

function update($id, array $data, int $userId): int
{
    $old = find_one($id, true);

    if (is_identic($old, $data)) {
        return 0;
    }

    $tags = $data['tags'];
    unset($data['tags']);

    $data['modified_by'] = $userId;
    $data['id'] = $id;

    $sql = "
        UPDATE articles
        SET 
          title = :title,
          content = :content,
          modified = NOW(),
          modified_by = :modified_by
        WHERE id = :id;
    ";

    DB::exec($sql, $data);

    save_tags($tags, $id, $userId, explode(',', $old['tag_ids']));

    return 1;
}

/**
 * @param array $tagIds
 * @param int $id
 * @return int
 */
function update_tag_ids(array $tagIds, int $id)
{
    $sql = "
        UPDATE articles
        SET tag_ids = :tags
        WHERE id = :id;
    ";
    $params = [
        'tags' => implode(',', $tagIds),
        'id' => $id
    ];
    return DB::exec($sql, $params);
}

function is_identic(array $old, array $new)
{
    if (strcmp($old['title'], $new['title']) !== 0) {
        return false;
    }
    if (strcmp($old['content'], $new['content']) !== 0) {
        return false;
    }

    $arrNewTags = explode(',',$new['tags']);
    $diff1 = array_udiff($old['tags'], $arrNewTags, "strcasecmp");
    $diff2 = array_udiff($arrNewTags, $old['tags'], "strcasecmp");
    if (!empty($diff1) || !empty($diff2)) {
        return false;
    }
    return true;
}

function delete($id)
{
    $tagIds = article_to_tag\find_tag_ids($id);
    article_to_tag\delete_tags($id);

    article_views\delete_by_article_id($id);

    // Counter in Tags aktualisieren
    tag\update_frequencies($tagIds);

    // Tags mit Counter=0 entfernen
    tag\delete_all_unused();

    return DB::exec("DELETE FROM articles WHERE id=:id;", ['id' => $id]);
}

function validate(array $data): array
{
    $errors = [];
    if (empty($data['title'])) {
        $errors['title'] = 'Bitte einen Titel eingeben';
    }
    if (empty($data['content'])) {
        $errors['content'] = 'Bitte einen Content eingeben';
    }
    if (empty($data['tags'])) {
        $errors['tags'] = 'Bitte Tags eingeben';
    }
    return $errors;
}

function find_all(string $q, array $tags, string $order, int $page, int $itemsPerPage): array
{
    $sql = "
        SELECT SQL_CALC_FOUND_ROWS a.id, a.title, GROUP_CONCAT(t.name) AS tags 
        FROM articles a 
        INNER JOIN tags t ON FIND_IN_SET(t.id, a.tag_ids)        
        WHERE 1=1
    ";

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

    $sql .= ' GROUP BY a.id';

    $orders = [
        'title' => 'a.title ASC',
        'changed' => 'a.modified DESC, a.title ASC',
        'created' => 'a.created DESC, a.title ASC',
        'default' => 'a.title ASC',
        'popular' => 'a.views DESC, a.title ASC'
    ];
    if (isset($orders[$order])) {
        $sql .= ' ORDER BY ' . $orders[$order];
    }

    $sql .= ' LIMIT ' . ($page - 1) * $itemsPerPage . ', ' . $itemsPerPage;

    $stmt = DB::prepare($sql);
    $stmt->execute($params);
    $articles = $stmt->fetchAll();

    foreach ($articles as $i => $a) {
        $articles[$i]['tags'] = explode(',', $a['tags']);
    }

    return $articles;
}

function found_rows(): int
{
    $sql = 'SELECT FOUND_ROWS()';
    $foundRows = DB::query($sql)->fetchColumn();
    return $foundRows;
}

function paging(int $totalCount, int $currentPage, int $itemsPerPage): array
{
    return [
        'itemsPerPage' => $itemsPerPage,
        'totalItems' => $totalCount,
        'currentPage' => $currentPage,
        'pageCount' => ceil($totalCount / $itemsPerPage)
    ];
}

function explode_tags(string $strtags): array
{
    $tags = explode(',', $strtags);
    $trimed = array_map('trim', $tags);
    $unique = array_iunique($trimed);
    return $unique;
}
