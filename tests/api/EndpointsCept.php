<?php

$I = new ApiTester($scenario);
$I->deleteHeader('Authorization');
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

// articles
$I->sendGET('/articles');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// articles filtered
$I->sendGET('/articles?sort=title&page=1');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->sendGET('/articles?sort=popular&page=1');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->sendGET('/articles?sort=changed&page=1');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->sendGET('/articles?sort=created&page=1');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// articles search
$I->sendGET('/articles?q=test&sort=created&page=1');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// articles paging
$I->sendGET('/articles?sort=created&page=2');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->sendGET('/articles?sort=created&page=3');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->sendGET('/articles?sort=created&page=9999999');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

/*
// article detail
$I->deleteHeader('Authorization');
$I->sendGET('/articles/6');
$I->seeResponseCodeIs(401);
$I->amBearerAuthenticated($token);
$I->sendGET('/articles/6');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
*/

// latest articles
$I->sendGET('/latest');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// popular articles
$I->sendGET('/popular');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// modified articles
$I->sendGET('/modified');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// liked articles
$I->sendGET('/liked');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// selected tags
$I->sendGET('/selectedtags');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// users
$I->sendGET('/users');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// user detail
$I->sendGET('/users/1');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// tags
$I->sendGET('/tags');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// tags filtered
$I->sendGET('/tags?sort=name');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->sendGET('/tags?sort=frequency');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->sendGET('/tags?sort=changed');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->sendGET('/tags?sort=created');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

// tag detail
$I->sendGET('/tags/3');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

