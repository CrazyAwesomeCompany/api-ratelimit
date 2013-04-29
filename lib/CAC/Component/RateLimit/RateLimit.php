<?php

namespace CAC\Component\RateLimit;

use CAC\Component\RateLimit\Storage\StorageInterface;

class RateLimit
{
    /**
     * Configuration
     *
     * @var array
     */
    private $config;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * Create a new RateLimiter
     *
     * @param StorageInterface $storage
     * @param array $config
     */
    public function __construct(StorageInterface $storage, $config = array())
    {
        $this->config = array_replace_recursive(
            array(
                'maxLimit' => 100,
                'regenerateTime' => 60,
                'regenerateAmount' => 10
            ),
            $config
        );

        $this->setStorage($storage);
    }

    /**
     * Substract an amount from the rate limit
     *
     * @param string  $id
     * @param integer $amount
     */
    public function substract($id, $amount)
    {
        $limit = $this->getCurrentLimit($id);
        $limit = max(0, ($limit - $amount));

        return $this->set($id, $limit);
    }

    /**
     * Set the new rate limit
     *
     * @param string  $id
     * @param integer $amount
     */
    public function set($id, $amount)
    {
        $amount = implode("|", array(time(), $amount));
        return $this->storage->set($id, $amount);
    }

    /**
     * Check if given id has reached the rate limit for the given resource amount
     *
     * @param mixed   $id
     * @param integer $amount
     *
     * @return bool
     */
    public function hasReachedLimit($id, $amount)
    {
        $limit = $this->getCurrentLimit($id);

        if ($limit < $amount) {
            return true;
        }

        return false;
    }

    /**
     * Get the current rate limit of the id given
     *
     * @param mixed $id
     *
     * @return integer
     */
    public function getCurrentLimit($id)
    {
        $currentLimit = $this->storage->fetch($id);

        // Check if the given ID already has a limit set. If not return the max limit
        if (false === $currentLimit) {
            return $this->config["maxLimit"];
        }

        // add some magic to determine amount of time passed since last add
        list($time, $credits) = explode("|", $currentLimit, 2);
        $additionalCredits = $this->calcCreditRegeneration($time);

        $credits += $additionalCredits;

        return min(intval($credits), $this->config["maxLimit"]);
    }

    /**
     * Calculate the amount of credits that should be added to credit balance
     *
     * @param integer $lastAdd timestamp
     *
     * @return integer
     */
    private function calcCreditRegeneration($lastAdd)
    {
        $credits = ((time() - $lastAdd) / $this->config["regenerateTime"]) * $this->config["regenerateAmount"];

        return round($credits);
    }

    /**
     * Set the RateLimit Storage Adapter
     *
     * @param StorageInterface $storage
     */
    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
    }
}
