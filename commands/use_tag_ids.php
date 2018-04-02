<?php

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', sprintf('%s/log/%s-error.log', dirname(__DIR__), date('Y-m')));

require '../vendor/autoload.php';

#die('Script died');

use function common\{
    pdo
};

$articles = find_all_article_tags();
$tags = find_all_tags();

print_r($articles);
print_r($tags);

// Test
foreach ($articles as $id => $article_tags) {
    $tag_ids = [];
    foreach ($article_tags as $article_tag) {
        if (!isset($tags[$article_tag])) {
            die ($article_tag . ' not exist in tags');
        }
    }
}

$sql = "UPDATE articles SET tag_ids = :tag_ids_csv WHERE id = :id;";
$stmt = pdo()->prepare($sql);

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

function update_article_tag_ids($id, array $tag_ids)
{

}

function find_all_tags()
{
    $sql = '
        SELECT id, LOWER(name) AS name
        FROM tags;
	';

    $stmt = pdo()->prepare($sql);
    $stmt->execute();

    $tags = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $name = $row['name'];
        $tags[$name] = $row['id'];
    }

    return $tags;
}

function find_all_article_tags()
{
    $sql = '
        SELECT id, LOWER(tags) AS tags
        FROM articles;
	';

    $stmt = pdo()->prepare($sql);
    $stmt->execute();

    $articles = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $id = $row['id'];
        $pos = strpos($row['tags'], ',');
        $tags = $pos === false ? [$row['tags']] : explode(',', $row['tags']);
        $articles[$id] = $tags;
    }

    return $articles;
}
