<?php

$I = new ApiTester($scenario);

$I->haveInDatabase('users', [
'username' => 'user'
]);
