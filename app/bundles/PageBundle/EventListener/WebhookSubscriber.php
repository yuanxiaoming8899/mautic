<?php

namespace Mautic\PageBundle\EventListener;

use Mautic\PageBundle\Event\PageHitEvent;
use Mautic\PageBundle\PageEvents;
use Mautic\WebhookBundle\Event\WebhookBuilderEvent;
use Mautic\WebhookBundle\Model\WebhookModel;
use Mautic\WebhookBundle\WebhookEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WebhookSubscriber implements EventSubscriberInterface
{
    private \Mautic\WebhookBundle\Model\WebhookModel $webhookModel;

    public function __construct(WebhookModel $webhookModel)
    {
        $this->webhookModel = $webhookModel;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            WebhookEvents::WEBHOOK_ON_BUILD => ['onWebhookBuild', 0],
            PageEvents::PAGE_ON_HIT         => ['onPageHit', 0],
        ];
    }

    /**
     * Add event triggers and actions.
     */
    public function onWebhookBuild(WebhookBuilderEvent $event): void
    {
        // add checkbox to the webhook form for new leads
        $pageHit = [
            'label'       => 'mautic.page.webhook.event.hit',
            'description' => 'mautic.page.webhook.event.hit_desc',
        ];

        // add it to the list
        $event->addEvent(PageEvents::PAGE_ON_HIT, $pageHit);
    }

    public function onPageHit(PageHitEvent $event): void
    {
        $this->webhookModel->queueWebhooksByType(
            PageEvents::PAGE_ON_HIT,
            [
                'hit' => $event->getHit(),
            ],
            [
                'hitDetails',
                'emailDetails',
                'pageList',
                'leadList',
            ]
        );
    }
}
