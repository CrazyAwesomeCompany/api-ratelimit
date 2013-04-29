<?php

namespace CAC\Component\RateLimit\Storage;

interface StorageInterface
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
