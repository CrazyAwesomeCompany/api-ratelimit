<?php

namespace CAC\Component\RateLimit\Storage;
use Doctrine\Common\Cache\Cache;

class DoctrineCacheStorage implements StorageInterface
{
    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * (non-PHPdoc)
     * @see \CAC\Component\RateLimit\Storage\StorageInterface::fetch()
     */
    public function fetch($id) {
        return $this->cache->fetch($id);
    }

    /**
     * (non-PHPdoc)
     * @see \CAC\Component\RateLimit\Storage\StorageInterface::set()
     */
    public function set($id, $amount) {
        return $this->cache->save($id, $amount);
    }
}
