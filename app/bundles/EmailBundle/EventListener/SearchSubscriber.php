<?php

namespace Mautic\EmailBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event as MauticEvents;
use Mautic\CoreBundle\Helper\UserHelper;
use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Mautic\EmailBundle\Model\EmailModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class SearchSubscriber implements EventSubscriberInterface
{
    private \Mautic\EmailBundle\Model\EmailModel $emailModel;

    private \Mautic\CoreBundle\Helper\UserHelper $userHelper;

    private \Mautic\CoreBundle\Security\Permissions\CorePermissions $security;

    private \Twig\Environment $twig;

    public function __construct(
        UserHelper $userHelper,
        EmailModel $emailModel,
        CorePermissions $security,
        Environment $twig
    ) {
        $this->userHelper = $userHelper;
        $this->emailModel = $emailModel;
        $this->security   = $security;
        $this->twig       = $twig;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::GLOBAL_SEARCH      => ['onGlobalSearch', 0],
            CoreEvents::BUILD_COMMAND_LIST => ['onBuildCommandList', 0],
        ];
    }

    public function onGlobalSearch(MauticEvents\GlobalSearchEvent $event): void
    {
        $str = $event->getSearchString();
        if (empty($str)) {
            return;
        }

        $filter      = ['string' => $str, 'force' => []];
        $permissions = $this->security->isGranted(
            ['email:emails:viewown', 'email:emails:viewother'],
            'RETURN_ARRAY'
        );
        if ($permissions['email:emails:viewown'] || $permissions['email:emails:viewother']) {
            if (!$permissions['email:emails:viewother']) {
                $filter['force'][] = [
                    'column' => 'IDENTITY(e.createdBy)',
                    'expr'   => 'eq',
                    'value'  => $this->userHelper->getUser()->getId(),
                ];
            }

            $emails = $this->emailModel->getEntities(
                [
                    'limit'  => 5,
                    'filter' => $filter,
                ]);

            if (count($emails) > 0) {
                $emailResults = [];

                foreach ($emails as $email) {
                    $emailResults[] = $this->twig->render(
                        '@MauticEmail/SubscribedEvents/Search/global.html.twig',
                        ['email' => $email]
                    );
                }
                if (count($emails) > 5) {
                    $emailResults[] = $this->twig->render(
                        '@MauticEmail/SubscribedEvents/Search/global.html.twig',
                        [
                            'showMore'     => true,
                            'searchString' => $str,
                            'remaining'    => (count($emails) - 5),
                        ]
                    );
                }
                $emailResults['count'] = count($emails);
                $event->addResults('mautic.email.emails', $emailResults);
            }
        }
    }

    public function onBuildCommandList(MauticEvents\CommandListEvent $event): void
    {
        if ($this->security->isGranted(['email:emails:viewown', 'email:emails:viewother'], 'MATCH_ONE')) {
            $event->addCommands(
                'mautic.email.emails',
                $this->emailModel->getCommandList()
            );
        }
    }
}
