<?php

namespace Mautic\WebhookBundle\Event;

use Mautic\CoreBundle\Event\CommonEvent;
use Mautic\WebhookBundle\Entity\Webhook;

class WebhookEvent extends CommonEvent
{
    /**
     * @var Webhook
     */
    protected $entity;

    /**
     * @var bool
     */
    protected $isNew = false;

    /**
     * @var string
     */
    private $reason = '';

    /**
     * @param bool $isNew
     */
    public function __construct(Webhook $webhook, $isNew = false, $reason = '')
    {
        $this->entity = $webhook;
        $this->isNew  = $isNew;
        $this->reason = $reason;
    }

    /**
     * Returns the Webhook entity.
     *
     * @return Webhook
     */
    public function getWebhook()
    {
        return $this->entity;
    }

    /**
     * Sets the Webhook entity.
     */
    public function setWebhook(Webhook $webhook): void
    {
        $this->entity = $webhook;
    }

    public function setReason($reason): void
    {
        $this->reason = $reason;
    }

    public function getReason(): string
    {
        return $this->reason;
    }
}
