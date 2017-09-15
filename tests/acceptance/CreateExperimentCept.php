<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Succeed at registering as a user and creating an experiment');

$I->amOnPage('/');
$I->waitForElement('#nav-login', 30);
$I->waitForElement('#nav-register', 30);

// Register
$I->click('#nav-register');
$I->wait(3);
$I->fillField('first_name', 'Tester');
$I->fillField('last_name', 'Robot');
$date = new DateTime();
$timestamp = $date->GetTimestamp();
$I->fillField('email', 'test_user.'.$timestamp.'@example.com');
$I->fillField('password', '123456');
$I->fillField('retype_password', '123456');
$I->click('#modal-register-submit');

// Check for success
$I->waitForElement('.-success', 30);
$I->waitForElement('#nav-logout', 30);
$I->waitForElement('#nav-profile', 30);
$I->seeCurrentUrlEquals('/fi/');

// Create an experiment
$I->amOnPage('/fi/new-experiment/0/');
$I->wait(3);
$I->fillField('title', 'Test Experiment');
$I->fillField('short_description', 'My automated test experiment');
$I->fillField('owner_name', 'Tester Robot ('.$timestamp.')');
$I->click('#new-experiment-submit');

// Check for success
$I->wait(10);
$I->seeCurrentUrlMatches('~\/fi\/experiment\/(\d+)\/~');
$I->see('Tester Robot');
$I->see('Tester Robot ('.$timestamp.')');

// Log out
$I->click('#nav-logout');
$I->wait(3);
$I->click('#modal-logout-submit');

// Check for success
$I->waitForElement('.-success', 30);
$I->waitForElement('#nav-login', 30);
$I->waitForElement('#nav-register', 30);
$I->seeCurrentUrlMatches('~\/fi\/experiment\/(\d+)\/~');
