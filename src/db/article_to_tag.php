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

    DB::exec($sql, [
        'article_id' => $articleId,
        'tag_ids' => $tagIds
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
            'article_id' => $articleId,
            'tag_id' => $tagId
        ]);

        $count = $selectStmt->fetchColumn();
        if ($count == 0) {
            $insertStmt->execute([
                'article_id' => $articleId,
                'tag_id' => $tagId
            ]);
        }

    }
}
