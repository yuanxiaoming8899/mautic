<?php

namespace Mautic\ReportBundle\EventListener;

use Mautic\ReportBundle\Event\ReportScheduleSendEvent;
use Mautic\ReportBundle\ReportEvents;
use Mautic\ReportBundle\Scheduler\Model\SendSchedule;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SchedulerSubscriber implements EventSubscriberInterface
{
    private \Mautic\ReportBundle\Scheduler\Model\SendSchedule $sendSchedule;

    public function __construct(SendSchedule $sendSchedule)
    {
        $this->sendSchedule = $sendSchedule;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ReportEvents::REPORT_SCHEDULE_SEND => ['onScheduleSend', 0],
        ];
    }

    public function onScheduleSend(ReportScheduleSendEvent $event): void
    {
        $scheduler = $event->getScheduler();
        $file      = $event->getFile();

        $this->sendSchedule->send($scheduler, $file);
    }
}
