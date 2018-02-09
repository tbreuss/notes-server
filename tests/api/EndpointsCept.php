<?php

// ping
$I = new ApiTester($scenario);
$I->wantTo('perform ping request and see result');
$I->sendGET('/ping');
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->seeResponseContains('"name":"ch.tebe.notes"');

// authenticate
$I->wantTo('authenticate a user');
#$I->haveHttpHeader('content-type', 'application/x-www-form-urlencoded');
$I->haveHttpHeader('Content-Type', 'application/json');
$I->sendPOST('/auth/login', json_encode([
    'username' => 'guest',
    'password' => 'guest'
]));
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

$token = $I->grabResponse();

// articles
$I->deleteHeader('Authorization');
$I->sendGET('/articles');
$I->seeResponseCodeIs(401);
$I->amBearerAuthenticated($token);
$I->sendGET('/articles');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// article detail
/*
$I->deleteHeader('Authorization');
$I->sendGET('/articles/10');
$I->seeResponseCodeIs(401);
$I->amBearerAuthenticated($token);
$I->sendGET('/articles/10');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
*/

// latest articles
$I->deleteHeader('Authorization');
$I->sendGET('/latest');
$I->seeResponseCodeIs(401);
$I->amBearerAuthenticated($token);
$I->sendGET('/latest');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// popular articles
$I->deleteHeader('Authorization');
$I->sendGET('/popular');
$I->seeResponseCodeIs(401);
$I->amBearerAuthenticated($token);
$I->sendGET('/popular');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// modified articles
$I->deleteHeader('Authorization');
$I->sendGET('/modified');
$I->seeResponseCodeIs(401);
$I->amBearerAuthenticated($token);
$I->sendGET('/modified');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// liked articles
$I->deleteHeader('Authorization');
$I->sendGET('/liked');
$I->seeResponseCodeIs(401);
$I->amBearerAuthenticated($token);
$I->sendGET('/liked');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// selected tags
$I->deleteHeader('Authorization');
$I->sendGET('/selectedtags');
$I->seeResponseCodeIs(401);
$I->amBearerAuthenticated($token);
$I->sendGET('/selectedtags');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// users
$I->deleteHeader('Authorization');
$I->sendGET('/users');
$I->seeResponseCodeIs(401);
$I->amBearerAuthenticated($token);
$I->sendGET('/users');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// user detail
$I->deleteHeader('Authorization');
$I->sendGET('/users/1');
$I->seeResponseCodeIs(401);
$I->amBearerAuthenticated($token);
$I->sendGET('/users/1');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// tags
$I->deleteHeader('Authorization');
$I->sendGET('/tags');
$I->seeResponseCodeIs(401);
$I->amBearerAuthenticated($token);
$I->sendGET('/tags');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// tag detail
$I->deleteHeader('Authorization');
$I->sendGET('/tags/3');
$I->seeResponseCodeIs(401);
$I->amBearerAuthenticated($token);
$I->sendGET('/tags/3');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

