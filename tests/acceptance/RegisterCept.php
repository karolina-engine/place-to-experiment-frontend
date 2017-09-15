<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Succeed at registering as a user and opening my profile page');

$I->amOnPage('/');
$I->waitForElement('#nav-login', 30);
$I->waitForElement('#nav-register', 30);

// Register
$I->click('#nav-register');
$I->wait(3);
$I->fillField('first_name', 'Tester');
$I->fillField('last_name', 'Robot');
$date = new DateTime();
$I->fillField('email', 'test_user.'.$date->GetTimestamp().'@example.com');
$I->fillField('password', '123456');
$I->fillField('retype_password', '123456');
$I->click('#modal-register-submit');

// Check for success
$I->waitForElement('.-success', 30);
$I->waitForElement('#nav-logout', 30);
$I->waitForElement('#nav-profile', 30);
$I->seeCurrentUrlEquals('/fi/');

// Open profile page
$I->click('#nav-profile');
$I->wait(3);
$I->seeCurrentUrlEquals('/fi/profile/me/');
$I->wait(3);
$I->see('Tester Robot');

// Log out
$I->click('#nav-logout');
$I->wait(3);
$I->click('#modal-logout-submit');

// Check for success
$I->wait(3);
$I->waitForElement('#nav-login', 30);
$I->waitForElement('#nav-register', 30);
$I->dontSeeCurrentUrlEquals('/fi/profile/me/');
$I->seeInCurrentUrl('/fi/profile/');
