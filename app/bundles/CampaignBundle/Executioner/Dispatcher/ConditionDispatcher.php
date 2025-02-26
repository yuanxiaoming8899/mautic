<?php

namespace Mautic\CampaignBundle\Executioner\Dispatcher;

use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Entity\LeadEventLog;
use Mautic\CampaignBundle\Event\ConditionEvent;
use Mautic\CampaignBundle\EventCollector\Accessor\Event\ConditionAccessor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ConditionDispatcher
{
    private \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function dispatchEvent(ConditionAccessor $config, LeadEventLog $log): ConditionEvent
    {
        $event = new ConditionEvent($config, $log);
        $this->dispatcher->dispatch($event, $config->getEventName());
        $this->dispatcher->dispatch($event, CampaignEvents::ON_EVENT_CONDITION_EVALUATION);

        return $event;
    }
}
