<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Doctrine\Common\Cache\FilesystemCache;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Kevinrob\GuzzleCache\Storage\DoctrineCacheStorage;


$routes = $app['controllers_factory'];

$routes
    ->assert('experimentId', '\d+')
    ->assert('lang', '(?:fi|en|sv)');


$routes->get('/', function() use($app) {

	return $app->redirect('/fi/');

});

$routes->get('/{lang}/', function() use($app) {

	$wpAPI = new Karolina\WpAPI('http://cms.kokeilunpaikka.fi');
	$posts = $wpAPI->getPostsIndex(1);

	$data['first_post'] = $posts[0];

	return $app['twig']->render('index.html', $data);

});


$routes->get('/{lang}/aloita/', function() use($app) {

   return $app['twig']->render('start.html');

});

$routes->get('/{lang}/sso', function() use($app) {

   return $app['twig']->render('sso.html');

});



$routes->get('/{lang}/logintodiscourse', function() use($app) {

	 // set the redirect URL
	 $data['redirectUrl'] = 'https://keskustelu.kokeilunpaikka.fi/session/sso';
   	return $app['twig']->render('login_to_discourse.html', $data);

});


$routes->get('/{lang}/list/', function() use($app) {

   return $app['twig']->render('admin.html');

});

$routes->get('/{lang}/leiki/', function() use($app) {

   return $app['twig']->render('leiki.html');

});

$routes->get('/{lang}/new-experiment/{experimentStage}/', function($experimentStage) use($app) {

	  $experimentStage = (int) $experimentStage;

    $data['experimentStage'] = $experimentStage;
    $data['experimentTag'] = '';
    return $app['twig']->render('new_experiment.html', $data);

});

$routes->get('/{lang}/new-experiment/{experimentStage}/{experimentTag}/', function($experimentStage, $experimentTag) use($app) {

	  $experimentStage = (int) $experimentStage;
	  $experimentTag = (int) $experimentTag;

    $data['experimentStage'] = $experimentStage;
    $data['experimentTag'] = $experimentTag;
    return $app['twig']->render('new_experiment.html', $data);

});


$routes->get('/{lang}/experiment/{experimentId}/', function(Request $request, $lang, $experimentId) use($app) {

	$data['lang'] = $lang;
    $client = new GuzzleHttp\Client();

    if (getenv('protocol') == "http") {

    	$config = ['verify' => false];

    } else {

    	$config = ['verify' => true];

    }

	$res = $client->request('GET', getenv('AGITATOR_URL').'/experiments/'.$experimentId.'/'.$lang, $config);
	$data['experiment_json'] = $res->getBody();

	$data['experimentId'] = $experimentId;
	$experimentData = json_decode($res->getBody(), true);

	if (isset($experimentData['experiment']['language']['title']['html'])) {

		$data['title'] = $experimentData['experiment']['language']['title']['value'];

	}

	if (isset($experimentData['experiment']['language']['short_description']['html'])) {

		$data['meta_description'] = $experimentData['experiment']['language']['short_description']['value'];

	}

	if (isset($experimentData['experiment']['image_collection']['top_image']['url'])) {

		$data['meta_image'] = $experimentData['experiment']['image_collection']['top_image']['url'];

	}

	if (isset($experimentData['experiment']['show_in']['index']) and $experimentData['experiment']['show_in']['index'] == true) {
		if (getenv('INDEX_LEIKI') == "yes") {
			$data['leiki_index'] = "true";
		}
	}

    return $app['twig']->render('experiment.html', $data);

})->assert('experimentId', '\d+');

$routes->get('/{lang}/experiment-embed/{experimentId}/', function(Request $request, $lang, $experimentId) use($app) {

	$data['lang'] = $lang;
    $client = new GuzzleHttp\Client();

    if (getenv('protocol') == "http") {

    	$config = ['verify' => false];

    } else {

    	$config = ['verify' => true];

    }

	$res = $client->request('GET', getenv('AGITATOR_URL').'/experiments/'.$experimentId.'/'.$lang, $config);
	$data['experiment_json'] = $res->getBody();

	$data['experimentId'] = $experimentId;
	$experimentData = json_decode($res->getBody(), true);

	if (isset($experimentData['experiment']['language']['title']['html'])) {

		$data['title'] = $experimentData['experiment']['language']['title']['value'];

	}

	if (isset($experimentData['experiment']['language']['short_description']['html'])) {

		$data['meta_description'] = $experimentData['experiment']['language']['short_description']['value'];

	}

	if (isset($experimentData['experiment']['image_collection']['top_image']['url'])) {

		$data['meta_image'] = $experimentData['experiment']['image_collection']['top_image']['url'];

	}

    return $app['twig']->render('experiment_embed.html', $data);

})->assert('experimentId', '\d+');

$routes->get('/{lang}/info/', function() use($app) {

	$wpAPI = new Karolina\WpAPI('http://cms.kokeilunpaikka.fi');
	$data['posts'] = $wpAPI->getPostsIndex(10);

	return $app['twig']->render('info.html', $data);

});
$routes->get('/{lang}/yhteystiedot/', function() use($app) {

   return $app['twig']->render('yhteystiedot.html');

});

$routes->get('/{lang}/haku/', function() use($app) {

   return $app['twig']->render('browse2.html');

});

$routes->get('/{lang}/haku2/', function() use($app) {

   return $app['twig']->render('browse2.html');

});

$routes->get('/{lang}/haku/stage/{stage}', function($stage) use($app) {

	$data['stage'] = $stage;
	return $app['twig']->render('browse_stage.html', $data);

});


$routes->get('/{lang}/kayttajia/', function() use($app) {

   return $app['twig']->render('users.html');

});


$routes->get('/{lang}/blogi.html', function() use($app) {

   return $app['twig']->render('blogi.html');

});

$routes->get('/{lang}/accelerator.html', function() use($app) {

   return $app['twig']->render('accelerator.html');

});


$routes->get('/{lang}/blogi-juhlan-paikka.html', function() use($app) {

   return $app['twig']->render('blogi-juhlan-paikka.html');

});


$routes->get('/{lang}/profile/{userId}/', function($userId) use($app) {

	if ($userId != "me") {
		$userId = (int) $userId;
	}

	$data['userId'] = $userId;
	return $app['twig']->render('profile.html', $data);

});

$routes->get('/{lang}/password-change/{resetToken}/', function($lang, $resetToken) use($app) {

	$data['resetToken'] = $resetToken;

	return $app['twig']->render('password_change.html', $data);

});

$routes->get('/{lang}/password-reset/', function($lang) use($app) {

	return $app['twig']->render('password_reset.html');

});


$routes->get('/{lang}/post/{id}/{slug}/', function($id, $slug) use($app) {

	$client = new GuzzleHttp\Client();
	$wpAPI = new Karolina\WpAPI('http://cms.kokeilunpaikka.fi');

	$data['posts'] = $wpAPI->getPostsIndex(10);
	$data['single_post'] = $wpAPI->getPost($id);

	$data['title'] = $data['single_post']['title'];

	return $app['twig']->render('post.html', $data);

});



$routes->get('/{lang}/custom/{template}/{slug}/', function($template, $slug) use($app) {

	if (!ctype_alnum($template)) {
		exit('template must be alphanum');
	}

	$client = new GuzzleHttp\Client();
	$wpAPI = new Karolina\WpAPI('http://cms.kokeilunpaikka.fi');

	$data['posts'] = $wpAPI->getPostsIndex(10);


	$client = new GuzzleHttp\Client();

	$res = $client->request('GET', 'http://cms.kokeilunpaikka.fi/wp-json/wp/v2/pages/?slug='.$slug);
	$wpData = json_decode($res->getBody(), true);

	$data['title'] = $wpData[0]['title']['rendered'];
	$data['content'] = $wpData[0]['content']['rendered'];
	return $app['twig']->render('custom_pages/'.$template.'.html', $data);


});

$routes->get('/{lang}/page/{slug}/', function($slug) use($app) {

	$client = new GuzzleHttp\Client();
	$wpAPI = new Karolina\WpAPI('http://cms.kokeilunpaikka.fi');

	$data['posts'] = $wpAPI->getPostsIndex(10);


	$client = new GuzzleHttp\Client();

	$res = $client->request('GET', 'http://cms.kokeilunpaikka.fi/wp-json/wp/v2/pages/?slug='.$slug);
	$wpData = json_decode($res->getBody(), true);

	$data['title'] = $wpData[0]['title']['rendered'];
	$data['content'] = $wpData[0]['content']['rendered'];
	return $app['twig']->render('page.html', $data);


});


return $routes;
