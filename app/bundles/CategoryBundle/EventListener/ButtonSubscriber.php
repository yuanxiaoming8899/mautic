<?php

namespace Mautic\CategoryBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\CustomButtonEvent;
use Mautic\CoreBundle\Twig\Helper\ButtonHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ButtonSubscriber implements EventSubscriberInterface
{
    private \Symfony\Component\Routing\RouterInterface $router;

    private \Symfony\Contracts\Translation\TranslatorInterface $translator;

    public function __construct(RouterInterface $router, TranslatorInterface $translator)
    {
        $this->router     = $router;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::VIEW_INJECT_CUSTOM_BUTTONS => ['injectContactBulkButtons', 0],
        ];
    }

    public function injectContactBulkButtons(CustomButtonEvent $event)
    {
        if (0 === strpos($event->getRoute(), 'mautic_contact_')) {
            $event->addButton(
                [
                    'attr' => [
                        'class'       => 'btn btn-default btn-sm btn-nospin',
                        'data-toggle' => 'ajaxmodal',
                        'data-target' => '#MauticSharedModal',
                        'href'        => $this->router->generate('mautic_category_batch_contact_view'),
                        'data-header' => $this->translator->trans('mautic.lead.batch.categories'),
                    ],
                    'btnText'   => $this->translator->trans('mautic.lead.batch.categories'),
                    'iconClass' => 'fa fa-cogs',
                ],
                ButtonHelper::LOCATION_BULK_ACTIONS
            );
        }
    }
}
