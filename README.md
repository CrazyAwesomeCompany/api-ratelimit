Crazy Awesome API Rate Limiting
===============================

Add Rate Limiting on your API. This provides limiting calls per User base. You can also specify weight to
different resources. The base principle about this can be found [here](http://amattn.com/2013/04/22/weighted_credit_pools_for_api_rate_limiting.html)

It's mandatory you use very fast storage backends when Rate Limiting in production environments. E.g. Redis.

## Configuration

When you start Rate Limiting you need to specify 3 configuration values

    $config = array(
        // Maximum limit for a User
        'maxLimit' => 100,
        // Amount of seconds to add new credits
        'regenerateTime' => 60,
        // Amount of credits to add
        'regenerateAmount' => 10
    );

## Usage

An example of checking if user has enough credits

    use CAC\Component\RateLimit\RateLimit;
    use CAC\Component\RateLimit\Storage\DoctrineCacheStorage;
    use Doctrine\Common\Cache\FilesystemCache;

    // In dev mode we can use anything as storage
    $cache = new FilesystemCache('/var/cache/ratelimit');
    $storage = new DoctrineCacheStorage($cache);
    $rateLimiter = new RateLimit($storage);

    $userId = $service->getUserId();

    // We want to know if the user has enough credits to request the resource costing 25 credits
    if ($rateLimiter->hasReachedLimit($userId, 25)) {
        throw new RateLimitException("Rate limit reached");
    }

    // Get resource...
    ...

    $rateLimiter->substract($userId, 25));

