<?php

namespace common;

use Medoo\Medoo as Medoo;
use PDO;

function _config()
{
    static $config;
    if (is_null($config)) {
        $filename = getenv('ENV', true);
        if (empty($filename)) {
            $filename = 'prod';
        }
        $filename = strtolower($filename);
        $config = require '../config/' . $filename . '.env.php';
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

function array_iunique($array) {
    $lowered = array_map('strtolower', $array);
    return array_intersect_key($array, array_unique($lowered));
}
