<?php

namespace Mautic\FormBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event as MauticEvents;
use Mautic\CoreBundle\Helper\UserHelper;
use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Mautic\FormBundle\Model\FormModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class SearchSubscriber implements EventSubscriberInterface
{
    private \Mautic\CoreBundle\Helper\UserHelper $userHelper;

    private \Mautic\FormBundle\Model\FormModel $formModel;

    private \Mautic\CoreBundle\Security\Permissions\CorePermissions $security;

    private \Twig\Environment $twig;

    public function __construct(
        UserHelper $userHelper,
        FormModel $formModel,
        CorePermissions $security,
        Environment $twig
    ) {
        $this->userHelper = $userHelper;
        $this->formModel  = $formModel;
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

        $filter = ['string' => $str, 'force' => ''];

        $permissions = $this->security->isGranted(['form:forms:viewown', 'form:forms:viewother'], 'RETURN_ARRAY');
        if ($permissions['form:forms:viewown'] || $permissions['form:forms:viewother']) {
            // only show own forms if the user does not have permission to view others
            if (!$permissions['form:forms:viewother']) {
                $filter['force'] = [
                    ['column' => 'f.createdBy', 'expr' => 'eq', 'value' => $this->userHelper->getUser()->getId()],
                ];
            }

            $forms = $this->formModel->getEntities(
                [
                    'limit'  => 5,
                    'filter' => $filter,
                ]);

            if (count($forms) > 0) {
                $formResults = [];
                foreach ($forms as $form) {
                    $formResults[] = $this->twig->render(
                        '@MauticForm/SubscribedEvents/Search/global.html.twig',
                        ['form' => $form[0]]
                    );
                }
                if (count($forms) > 5) {
                    $formResults[] = $this->twig->render(
                        '@MauticForm/SubscribedEvents/Search/global.html.twig',
                        [
                            'showMore'     => true,
                            'searchString' => $str,
                            'remaining'    => (count($forms) - 5),
                        ]
                    );
                }
                $formResults['count'] = count($forms);
                $event->addResults('mautic.form.forms', $formResults);
            }
        }
    }

    public function onBuildCommandList(MauticEvents\CommandListEvent $event): void
    {
        if ($this->security->isGranted(['form:forms:viewown', 'form:forms:viewother'], 'MATCH_ONE')) {
            $event->addCommands(
                'mautic.form.forms',
                $this->formModel->getCommandList()
            );
        }
    }
}
