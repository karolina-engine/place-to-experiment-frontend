<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

// COMPOSER
require_once '../vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();


/**********************
PROTOCOL FORWARD
***********************/
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {

	$protocol_array = explode(',', $_SERVER['HTTP_X_FORWARDED_PROTO']);
	$current_protocol = $protocol_array[0];

} else {

	if (isset($_SERVER['HTTPS'])) {
		$current_protocol = 'https';
	} else {
		$current_protocol = 'http';
	}
}


// Forward us to a secure protocol, if we are not on one
if (getenv('protocol') == 'https' and $current_protocol != 'https') {

	header('Location: https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
	exit;

}

$app = new Silex\Application();

$app['debug'] = false;


if (getenv('ENVIRONMENT') == 'development') {
	$app['debug'] = true;
}


/*********************************************
	Translation
*********************************************/

$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('fi'),
));


$app->extend('translator', function($translator, $app) {

		$translator->addLoader('mo', new Symfony\Component\Translation\Loader\MoFileLoader());

		$translator->addResource('mo', __DIR__ . '/../translations/fi.mo', 'fi');
		$translator->addResource('mo', __DIR__ . '/../translations/en.mo', 'en');
		$translator->addResource('mo', __DIR__ . '/../translations/sv.mo', 'sv');

		$translator->setFallbackLocales(array('fi'));


		return $translator;

});

$firstPath = explode('/', $_SERVER['REQUEST_URI']);
if ($firstPath[1] == "fi" or $firstPath[1] == "en" or $firstPath[1] == "sv") {

	$lang = $firstPath[1];
	$app['lang'] = $lang;
} else {

	header('location: /fi'.$_SERVER['REQUEST_URI']);
	exit();
}




$app->before(function (Request $request, $app) {


	$app['translator']->setLocale($app['lang']);
	$app['twig']->addGlobal('lang', $app['lang']);

	$app['twig']->addGlobal('current_page', $request->getRequestUri());

	$securityHeaders = new \Karolina\Response\SecurityHeaders();
	$securityHeaders->set();

});


$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../templates',
));


$app['twig']->addFilter(new Twig_SimpleFilter('langurl', function ($url, $langFrom, $langTo) {

	return str_replace("/".$langFrom."/", "/".$langTo."/", $url);

}));


$app['twig']->addGlobal('agitator_url', getenv('AGITATOR_URL'));
$app['twig']->addGlobal('title', 'Kokeilun Paikka');
$app['twig']->addGlobal('meta_description', 'Kokeilun paikka on palvelu, jossa me kaikki voimme ideoida ja kehittää yhteisiä kokeiluja sekä hakea niille rahoitusta. Kokeilun paikassa edetään ideasta / haasteesta kokeiluehdotukseen, mahdolliseen rahoitukseen ja toteutukseen asti.');
$app['twig']->addGlobal('meta_image', getenv('PROTOCOL').'://'.$_SERVER['HTTP_HOST'].'/img/logo.svg');
$app['twig']->addGlobal('allow_discussion', getenv('ALLOW_DISCUSSION'));
$app['twig']->addGlobal('leiki_index', "false");

$lexer = new Twig_Lexer($app['twig'], array(
    'tag_variable' => array('${', '}')
));

$app['twig']->setLexer($lexer);

$app->mount('/', include __DIR__.'/routes.php');
$app->run();
