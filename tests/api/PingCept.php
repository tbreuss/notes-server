<?php 
$I = new ApiTester($scenario);
$I->wantTo('perform ping request and see result');
$I->sendGET('/ping');
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->seeResponseContains('"name":"ch.tebe.notes"');
