<?php

namespace jwt;

use Firebase\JWT\JWT as FirebaseJWT;
use function common\{
    config
};

/**
 * @see https://stackoverflow.com/questions/40582161/how-to-properly-use-bearer-tokens
 */
function get_authorization_header()
{
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        //print_r($requestHeaders);
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

function get_bearer_token()
{
    $headers = get_authorization_header();
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

function generate_token(array $user): string
{
    $payload = array(
        "iss" => $_SERVER['HTTP_ORIGIN'],
        "iat" => time(),
        //"exp" => time() + (60*60*24),
        'user' => [
            'id' => $user['id'],
            'name' => $user['username']
        ]
    );
    $key = config('jwt_private_key');
    $jwt = FirebaseJWT::encode($payload, $key, 'HS256');
    return $jwt;
}

function get_user_from_token(): array
{
    $jwt = get_bearer_token();
    $decoded = FirebaseJWT::decode($jwt, config('jwt_private_key'), array('HS256'));
    return (array)$decoded->user;
}