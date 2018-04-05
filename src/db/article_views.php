<?php

namespace db\article_views;

use DB;

function find_latest_date(int $articleId): string
{
    $sql = "
        SELECT created
        FROM article_views
        WHERE article_id = :article_id
        ORDER BY created DESC
        LIMIT 1;
    ";

    $date = DB::query($sql, ['article_id' => $articleId])->fetchColumn();
    return empty($date) ? '' : $date;
}

function delete_by_article_id(int $articleId): int
{
    $sql = "
      DELETE FROM article_views 
      WHERE article_id = :article_id;
    ";
    $numRows = DB::exec($sql, [
        ':article_id' => $articleId
    ]);
    return $numRows;
}
