<?php

namespace db\user;

use DB;

function authenticate(string $username, string $password): array
{
    $user = find_one($username);
    if (!empty($user)) {
        if (validate_password($password, $user)) {
            update_last_login($username);
            return $user;
        }
    }
    return [];
}

function find_all(string $sort): array
{
    $orders = [
        #'name' => ['name' => 'ASC'],
        #'frequency' => ['frequency' => 'DESC', 'name' => 'ASC'],
        #'changed' => ['modified' => 'DESC', 'name' => 'ASC'],
        #'created' => ['created' => 'DESC', 'name' => 'ASC'],
        'default' => 'name ASC'
    ];
    $order = isset($orders[$sort]) ? $orders[$sort] : $orders['default'];
    $sql = "
        SELECT id, name, article_likes, article_views, lastlogin, created, modified
        FROM users
        WHERE deleted = 0
        ORDER BY ${order};
    ";
    $users = DB::query($sql)->fetchAll();
    return $users;
}

function find_by_user_ids(array $ids): array
{
    $ids = array_filter($ids);
    if (empty($ids)) {
        return [];
    }

    $sql = "
        SELECT id AS pk, id, username, name, email, lastlogin, created, modified, deleted
        FROM users
        WHERE id IN (:id);
    ";
    $users = DB::query($sql, ['id' => $ids])->fetchAll(DB::instance()::FETCH_UNIQUE);

    return $users;
}

function update_last_login(string $username): int
{
    $sql = "UPDATE users SET lastlogin = NOW() WHERE username = :username;";
    $rowCount = DB::exec($sql, ['username' => $username]);
    return $rowCount;
}

function find_one(string $username): array
{
    $sql = "
        SELECT *
        FROM users
        WHERE deleted = 0
        AND username = :username;
    ";
    $params = ['username' => $username];
    $user = DB::query($sql, $params)->fetch();
    if (empty($user)) {
        return [];
    }
    return $user;
}

function validate_password(string $password, array $user): bool
{
    return hash_password($password, $user['salt']) === $user['password'];
}

function hash_password(string $password, string $salt): string
{
    return md5($salt . $password);
}

function generate_salt(): string
{
    return uniqid('', true);
}

function validate_credentials(array $data): array
{
    $errors = [];
    if (empty($data['username'])) {
        $errors['username'] = 'Benutzername fehlt';
    }
    if (empty($data['password'])) {
        $errors['password'] = 'Passwort fehlt';
    }
    return $errors;
}

function create(array $data): int
{
    // username, password, name, email

    $salt = generate_salt();
    $password = hash_password($data['password'], $salt);

    $data['password'] = $password;
    $data['salt'] = $salt;

    $sql = "
        INSERT users (username, password, salt, name, email, created)
        VALUES (:username, :password, :salt, :name, :email, NOW());
    ";

    DB::exec($sql, $data);
    return DB::lastInsertId();
}

function renew_password(string $username, string $password): int
{
    $salt = generate_salt();

    $sql = "
        UPDATE users
        SET 
          password = :password, 
          salt = :salt, 
          modified = NOW()
        WHERE username = :username;        
    ";

    $params = [
        'password' => hash_password($password, $salt),
        'salt' => $salt,
        'username' => $username
    ];

    $rowCount = DB::exec($sql, $params);
    return $rowCount;
}
