<?php

namespace db\article_to_tag;

use DB;

function insert(int $articleId, array $tagIds)
{
    $sql = 'INSERT INTO article_to_tag VALUES (:article_id, :tag_id, NOW())';
    $stmt = DB::prepare($sql);
    $bool = true;

    foreach ($tagIds as $tagId) {
        $bool &= $stmt->execute([
            ':article_id' => $articleId,
            ':tag_id' => $tagId
        ]);
    }

    return $bool;
}

function delete_tags(int $articleId)
{
    $sql = "
      DELETE FROM article_to_tag 
      WHERE article_id = :article_id;
    ";
    $stmt = DB::prepare($sql);
    $bool = $stmt->execute([
        ':article_id' => $articleId
    ]);
    return $bool;
}

function save_tags(int $articleId, array $tagIds)
{
    // delete
    $sql = "
        DELETE FROM article_to_tag 
        WHERE article_id = :article_id
        AND tag_id NOT IN (:tag_ids);
    ";

    DB::prepare($sql)->execute([
        ':article_id' => $articleId,
        ':tag_ids' => implode(',', $tagIds)
    ]);

    $selectSql = "
        SELECT COUNT(*) AS count
        FROM article_to_tag
        WHERE article_id = :article_id
        AND tag_id = :tag_id;        
    ";
    $selectStmt = DB::prepare($selectSql);

    $insertSql = "
        INSERT INTO article_to_tag 
        VALUES (:article_id, :tag_id, NOW())
    ";
    $insertStmt = DB::prepare($insertSql);

    foreach ($tagIds as $tagId) {

        $selectStmt->execute([
            ':article_id' => $articleId,
            ':tag_id' => $tagId
        ]);

        $count = $selectStmt->fetchColumn();
        if ($count == 0) {
            $insertStmt->execute([
                ':article_id' => $articleId,
                ':tag_id' => $tagId
            ]);
        }

    }
}

/*
 * $sql = "UPDATE articles SET tag_ids = :tag_ids_csv WHERE id = :id;";
$stmt = PDO::prepare($sql);

// Update
foreach ($articles as $id => $article_tags) {
    $tag_ids = [];
    foreach ($article_tags as $article_tag) {
        if (!isset($tags[$article_tag])) {
            die ('xxx');
        }
        $tag_ids[] = $tags[$article_tag];
    }

    $bool = $stmt->execute(array(':id' => $id, ':tag_ids_csv' => implode(',', $tag_ids)));

}

 */