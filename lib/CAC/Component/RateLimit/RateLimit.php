<?php

namespace CAC\Component\RateLimit;


class RateLimit
{

    private $maxLimit;


    private $limitRegenerateTime = 60;
    private $limitRegenerateAmount = 10;


    /**
     * @var RateLimitStorageInterface
     */
    private $storage;


    public function __construct(RateLimitStorageInterface $storage, $config = array())
    {
        $this->setStorage($storage);
        $this->maxLimit = 100;
    }

    public function substract($id, $amount)
    {
        $limit = $this->getCurrentLimit($id);
        $limit = max(0, ($limit - $amount));

        return $this->set($id, $limit);
    }

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
            return $this->maxLimit;
        }

        // add some magic to determine amount of time passed since last add
        list($time, $credits) = explode("|", $currentLimit, 2);
        $additionalCredits = $this->calcCreditRegeneration($time);

        $credits += $additionalCredits;

        return min(intval($credits), $this->maxLimit);
    }

    /**
     * Calculate the amount of credits that should be added to credit balance
     *
     * @param integer $lastAdd timestamp
     *
     * @return array
     */
    private function calcCreditRegeneration($lastAdd)
    {
        $credits = ((time() - $lastAdd) / $this->limitRegenerateTime) * $this->limitRegenerateAmount;

        return round($credits);
    }

    /**
     * Set the RateLimit Storage Adapter
     *
     * @param RateLimitStorageInterface $storage
     */
    public function setStorage(RateLimitStorageInterface $storage)
    {
        $this->storage = $storage;
    }
}
