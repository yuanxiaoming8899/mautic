<?php

namespace Mautic\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class UpgradeEvent extends Event
{
    protected array $status;

    public function __construct(array $status)
    {
        $this->status = $status;
    }

    /**
     * @return array
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function isSuccessful()
    {
        if (array_key_exists('success', $this->status)) {
            return (bool) $this->status['success'];
        }

        return false;
    }
}
