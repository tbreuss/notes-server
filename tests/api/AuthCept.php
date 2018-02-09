<?php

$I = new ApiTester($scenario);
$I->deleteHeader('Authorization');

// ping
$I->wantTo('perform ping request and see result');
$I->sendGET('/ping');
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->seeResponseContains('"name":"ch.tebe.notes"');

$endpoints = [
    '/articles' => 'request articles list',
    #'/articles/6' => 'request article detail',
    '/latest' => 'request latest articles',
    '/popular' => 'request popular articles',
    '/modified' => 'request modified articles',
    '/liked' => 'request liked articles',
    '/selectedtags' => 'request selected tags',
    '/users' => 'request user list',
    '/users/1' => 'request user detail',
    '/tags' => 'request tag list',
    '/tags/3' => 'request tag detail'
];

foreach ($endpoints as $url => $wantTo) {
    $I->wantTo($wantTo);
    $I->sendGET($url);
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::UNAUTHORIZED); // 401
}

// authenticate
$I->wantTo('authenticate a user');
$I->haveHttpHeader('content-type', 'application/x-www-form-urlencoded');
$I->haveHttpHeader('Content-Type', 'application/json');
$I->sendPOST('/auth/login', json_encode([
    'username' => 'guest',
    'password' => 'guest'
]));
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->amBearerAuthenticated($I->grabResponse());

foreach ($endpoints as $url => $wantTo) {
    $I->wantTo($wantTo);
    $I->sendGET($url);
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
}
