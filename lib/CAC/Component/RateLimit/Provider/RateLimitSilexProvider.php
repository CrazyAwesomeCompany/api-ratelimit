<?php

namespace CAC\Component\RateLimit\Provider;

use CAC\Component\RateLimit\RateLimitException;

use Symfony\Component\HttpFoundation\Request;

use Silex\Application;
use Silex\ServiceProviderInterface;
use CAC\Component\RateLimit\RateLimit;
use CAC\Component\RateLimit\Storage\DoctrineCacheStorage;
use Doctrine\Common\Cache\FilesystemCache;

class RateLimitSilexProvider implements ServiceProviderInterface {

    public function register(Application $app) {
        $app['cac.ratelimit'] = $app->share(function() {
            // Create Cache
            $cache = new FilesystemCache(__DIR__ . '/../../../../../cache');
            // Create RateLimitStorage Object
            $storage = new DoctrineCacheStorage($cache);
            // Create RateLimiter
            $rateLimiter = new RateLimit($storage);

            return $rateLimiter;
        });

        $app->before(function(Request $request) use ($app) {
            $userId = md5(1);

//             if ($app['cac.ratelimit']->hasReachedLimit($userId, 23)) {
//                 throw new RateLimitException("Rate limit reached");
//             };
        }, 899);
    }


    public function boot(Application $app) {

    }

}
