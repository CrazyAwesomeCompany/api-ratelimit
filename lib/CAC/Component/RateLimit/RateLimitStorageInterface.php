<?php

namespace CAC\Component\RateLimit;


interface RateLimitStorageInterface
{
    /**
     * Fetch rate limit
     *
     * @param string $id
     */
    public function fetch($id);

    /**
     * Set a new rate limit
     *
     * @param string $id
     * @param string $amount
     */
    public function set($id, $amount);

}
