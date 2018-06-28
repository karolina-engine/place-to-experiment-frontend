<?php

namespace Karolina\Response;

use \ParagonIE\CSPBuilder\CSPBuilder;

Class SecurityHeaders {

    public function set () {

        $csp = CSPBuilder::fromFile(__DIR__ . '/csp.json');

        $csp->addSource('image', "d1ncrxda1lmimh.cloudfront.net");
        $csp->addSource('image', "s3-eu-west-1.amazonaws.com");
        $csp->addSource('image', "*.google-analytics.com");
        $csp->addSource('image', "*.facebook.com");
        $csp->addSource('image', "*.kokeilunpaikka.fi");
        $csp->addSource('image', "*.gstatic.com");
        $csp->addSource('image', "*.googleapis.com");
        $csp->addSource('image', "*.twitter.com");
        $csp->addSource('image', "*.twimg.com");

        $csp->addSource('connect-src', "*.karolina.io");
        $csp->addSource('connect-src', "*.kokeilunpaikka.fi");
        $csp->addSource('connect-src', "*.leiki.com");

        $csp->addSource('script-src', "*.jquery.com");
        $csp->addSource('script-src', "*.youtube.com");
        $csp->addSource('script-src', "*.ytimg.com");

        $csp->addSource('script-src', "*.facebook.net");
        $csp->addSource('script-src', "*.google-analytics.com");
        $csp->addSource('script-src', "*.kokeilunpaikka.fi");
        $csp->addSource('script-src', "cdnjs.cloudflare.com");
        $csp->addSource('script-src', "*.leiki.com");
        $csp->addSource('script-src', "*.googleapis.com");
        $csp->addSource('script-src', "*.twitter.com");
        $csp->addSource('script-src', "*.twimg.com");

        if (getenv('ENVIRONMENT') == "development") {

            $csp->addSource('script-src', "place-to-experiment-backend.test");
            $csp->addSource('connect-src', "place-to-experiment-backend.test");
            $csp->addSource('script-src', "place-to-experiment-backend.test");
            $csp->addSource('script-src', "place-to-experiment-frontend.test");
            $csp->addSource('script-src', "place-to-experiment.test");
            $csp->addSource('connect-src', "place-to-experiment.test");

        }

        $csp->addSource('style-src', "cdnjs.cloudflare.com");
        $csp->addSource('style-src', "hello.myfonts.net");
        $csp->addSource('style-src', "*.googleapis.com");
        $csp->addSource('style-src', "*.twitter.com");

    	header("strict-transport-security: max-age=1200");
    	header("X-XSS-Protection: 1; mode=block");
    	header("X-Content-Type-Options: nosniff");
  //  	header("X-Frame-Options: SAMEORIGIN");

    	$csp->sendCSPHeader();

    }

}
