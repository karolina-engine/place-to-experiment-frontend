<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Succeed at logging in and out as admin');

$I->amOnPage('/');
$I->waitForElement('#nav-login', 30);
$I->waitForElement('#nav-register', 30);

// Log in
$I->click('#nav-login');
$I->wait(3);
$I->fillField('email', admin_email);
$I->fillField('password', admin_password);
$I->click('#modal-login-submit');

// Check for success
$I->waitForElement('.-success', 30);
$I->waitForElement('#nav-logout', 30);
$I->waitForElement('#nav-profile', 30);
$I->seeCurrentUrlEquals('/fi/');

// Log out
$I->click('#nav-logout');
$I->wait(3);
$I->click('#modal-logout-submit');

// Check for success
$I->waitForElement('.-success', 30);
$I->waitForElement('#nav-login', 30);
$I->waitForElement('#nav-register', 30);
$I->seeCurrentUrlEquals('/fi/');
