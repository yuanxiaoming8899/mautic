<?php

namespace Mautic\LeadBundle\Event;

use Mautic\LeadBundle\Entity\DoNotContact;
use Mautic\LeadBundle\Entity\Lead;
use Symfony\Contracts\EventDispatcher\Event;

class ChannelSubscriptionChange extends Event
{
    private \Mautic\LeadBundle\Entity\Lead $lead;

    /**
     * @var string
     */
    private $channel;

    /**
     * @var string
     */
    private $oldStatus;

    /**
     * @var string
     */
    private $newStatus;

    public function __construct(Lead $lead, $channel, $oldStatus, $newStatus)
    {
        $this->lead      = $lead;
        $this->channel   = $channel;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * @return Lead
     */
    public function getLead()
    {
        return $this->lead;
    }

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return int
     */
    public function getOldStatus()
    {
        return $this->oldStatus;
    }

    /**
     * @return string
     */
    public function getOldStatusVerb()
    {
        return $this->getDncReasonVerb($this->oldStatus);
    }

    /**
     * @return int
     */
    public function getNewStatus()
    {
        return $this->newStatus;
    }

    /**
     * @return string
     */
    public function getNewStatusVerb()
    {
        return $this->getDncReasonVerb($this->newStatus);
    }

    /**
     * @return string
     */
    private function getDncReasonVerb($reason)
    {
        // use true matching or else 'foobar' == DoNotContact::IS_CONTACTABLE
        switch (true) {
            case DoNotContact::IS_CONTACTABLE === $reason:
                return 'contactable';
            case DoNotContact::BOUNCED === $reason:
                return 'bounced';
            case DoNotContact::MANUAL === $reason:
                return 'manual';
            default:
                return 'unsubscribed';
        }
    }
}
