<?php

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', sprintf('%s/log/%s-error.log', dirname(__DIR__), date('Y-m')));

require '../vendor/autoload.php';

try {

    DB::init([
        'host' => '127.0.0.1',
        'dbname' => 'notes_server_dev',
        'username' => 'root',
        'passwd' => 'root',
        'port' => '8889'
    ]);

    $sql = "
        SELECT * 
        FROM tags
        WHERE name IN (:names)
        AND name IS NOT :name
        AND id >= :id
        AND created_by = :created_by
        AND frequency IN (:frequencies);
    ";
    $params = [
        'id' => true,
        'name' => null,
        'created_by' => 1,
        'names' => [10, 11, 12, 'ABC'],
        'frequencies' => [0, 1, 2]
    ];
    $rows = DB::query($sql, $params)->fetchAll();
    out($rows);
    echo DB::getLastQuery();

    $sql = "
        SELECT id, name 
        FROM tags
        ORDER BY frequency DESC;
    ";
    $rows = DB::query($sql)->fetchAll(PDO::FETCH_KEY_PAIR);
    out($rows);
    echo DB::getLastQuery();

    $sql = "
        SELECT id, name, frequency 
        FROM tags
        ORDER BY frequency DESC;
    ";
    $rows = DB::query($sql)->fetchAll(PDO::FETCH_UNIQUE);
    out($rows);
    echo DB::getLastQuery();

    $sql = "
        SELECT * 
        FROM articles;
    ";
    $rows = DB::query($sql)->fetchAll();
    out($rows);
    echo DB::getLastQuery();

    $sql = "
        SELECT * 
        FROM articles
        WHERE FIND_IN_SET(33, tag_ids);
    ";

    $rows = DB::query($sql)->fetchAll();
    out($rows);
    echo DB::getLastQuery();

    $sql = "
        SELECT * 
        FROM articles
        WHERE tags = :tags
        AND created > :created;
    ";
    $params = [
        'tags' => 'ABC',
        'created' => '2018-03-31 15:31:49'
    ];
    $rows = DB::query($sql, $params)->fetchAll();
    out($rows);
    echo nl2br(DB::getLastQuery());

    $sql = "
        INSERT INTO tags (name, created, created_by)
        VALUES (:name, :created, :created_by);
    ";

    DB::exec($sql, [
        'name' => 'Hans',
        'created' => 'NOW()',
        'created_by' => 2
    ]);


} catch (\Exception $e) {

    echo $e->getMessage();
    echo "<pre>";
    print_r($e->getTrace());
    echo "</pre>";

}

function out($what)
{
    echo "<pre>";
    print_r($what);
    echo "</pre>";
}
