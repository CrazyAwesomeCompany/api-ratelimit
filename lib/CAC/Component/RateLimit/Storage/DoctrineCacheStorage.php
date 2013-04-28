<?php

namespace CAC\Component\RateLimit\Storage;
use Doctrine\Common\Cache\Cache;

use CAC\Component\RateLimit\RateLimitStorageInterface;

class DoctrineCacheStorage implements RateLimitStorageInterface
{

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function fetch($id) {
        return $this->cache->fetch($id);
    }

    public function set($id, $amount) {
        return $this->cache->save($id, $amount);
    }
}
