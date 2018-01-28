<?php

namespace common;

use Medoo\Medoo as Medoo;
use PDO;

function _config()
{
    static $config;
    if (is_null($config)) {
        $config = require '../config/main.php';
    }
    return $config;
}

function config(string $name, $default = null)
{
    $path = explode('.', $name);
    $current = _config();
    foreach ($path as $field) {
        if (isset($current) && isset($current[$field])) {
            $current = $current[$field];
        } elseif (is_array($current) && isset($current[$field])) {
            $current = $current[$field];
        } else {
            return $default;
        }
    }
    return $current;
}

function medoo(): Medoo
{
    return database();
}

function database(): Medoo
{
    static $database;
    if (is_null($database)) {
        $config = config('database', []);
        $database = new Medoo($config);
    }
    return $database;
}

function pdo(): PDO
{
    $database = database();
    return $database->pdo;
}

function array_iunique($array) {
    $lowered = array_map('strtolower', $array);
    return array_intersect_key($array, array_unique($lowered));
}
