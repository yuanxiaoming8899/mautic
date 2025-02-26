<?php

namespace Mautic\AssetBundle\EventListener;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Mautic\AssetBundle\Entity\Asset;
use Mautic\AssetBundle\Form\Type\FormSubmitActionDownloadFileType;
use Mautic\AssetBundle\Model\AssetModel;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Helper\ThemeHelperInterface;
use Mautic\CoreBundle\Twig\Helper\AnalyticsHelper;
use Mautic\CoreBundle\Twig\Helper\AssetsHelper;
use Mautic\FormBundle\Entity\Form;
use Mautic\FormBundle\Event\FormBuilderEvent;
use Mautic\FormBundle\Event\SubmissionEvent;
use Mautic\FormBundle\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class FormSubscriber implements EventSubscriberInterface
{
    private \Mautic\AssetBundle\Model\AssetModel $assetModel;

    protected \Symfony\Contracts\Translation\TranslatorInterface $translator;

    private \Mautic\CoreBundle\Twig\Helper\AnalyticsHelper $analyticsHelper;

    private \Mautic\CoreBundle\Twig\Helper\AssetsHelper $assetsHelper;

    private \Mautic\CoreBundle\Helper\ThemeHelperInterface $themeHelper;

    private \Twig\Environment $twig;

    private \Mautic\CoreBundle\Helper\CoreParametersHelper $coreParametersHelper;

    public function __construct(
        AssetModel $assetModel,
        TranslatorInterface $translator,
        AnalyticsHelper $analyticsHelper,
        AssetsHelper $assetsHelper,
        ThemeHelperInterface $themeHelper,
        Environment $twig,
        CoreParametersHelper $coreParametersHelper
    ) {
        $this->assetModel           = $assetModel;
        $this->translator           = $translator;
        $this->analyticsHelper      = $analyticsHelper;
        $this->assetsHelper         = $assetsHelper;
        $this->themeHelper          = $themeHelper;
        $this->twig                 = $twig;
        $this->coreParametersHelper = $coreParametersHelper;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::FORM_ON_BUILD                 => ['onFormBuilder', 0],
            FormEvents::ON_EXECUTE_SUBMIT_ACTION      => [
                ['onFormSubmitActionAssetDownload', 0],
                ['onFormSubmitActionDownloadFile', 0],
            ],
        ];
    }

    /**
     * Add a lead generation action to available form submit actions.
     */
    public function onFormBuilder(FormBuilderEvent $event): void
    {
        $event->addSubmitAction('asset.download', [
            'group'              => 'mautic.asset.actions',
            'label'              => 'mautic.asset.asset.submitaction.downloadfile',
            'description'        => 'mautic.asset.asset.submitaction.downloadfile_descr',
            'formType'           => FormSubmitActionDownloadFileType::class,
            'formTypeCleanMasks' => ['message' => 'html'],
            'eventName'          => FormEvents::ON_EXECUTE_SUBMIT_ACTION,
            'allowCampaignForm'  => true,
        ]);
    }

    public function onFormSubmitActionAssetDownload(SubmissionEvent $event): void
    {
        if (false === $event->checkContext('asset.download')) {
            return;
        }

        $properties = $event->getAction()->getProperties();
        $assetId    = $properties['asset'];
        $categoryId = $properties['category'] ?? null;
        $asset      = null;

        if (null !== $assetId) {
            $asset = $this->assetModel->getEntity($assetId);
        } elseif (null !== $categoryId) {
            try {
                $asset = $this->assetModel->getRepository()->getLatestAssetForCategory($categoryId);
            } catch (NoResultException|NonUniqueResultException $e) {
                $asset = null;
            }
        }

        if ($asset instanceof Asset && $asset->isPublished()) {
            $event->setPostSubmitCallback('asset.download_file', [
                'eventName' => FormEvents::ON_EXECUTE_SUBMIT_ACTION,
                'form'      => $event->getAction()->getForm(),
                'asset'     => $asset,
                'message'   => $properties['message'] ?? '',
            ]);
        }
    }

    public function onFormSubmitActionDownloadFile(SubmissionEvent $event): void
    {
        if (false === $event->checkContext('asset.download_file')) {
            return;
        }

        /*
         * No further actions can run after this, as we need to send the
         * download response to the client.
         */
        $event->stopPropagation();

        /**
         * @var Form   $form
         * @var Asset  $asset
         * @var string $message
         * @var bool   $messengerMode
         */
        [
            'form'          => $form,
            'asset'         => $asset,
            'message'       => $message,
            'messengerMode' => $messengerMode,
        ]    = $event->getPostSubmitCallback('asset.download_file');

        $url = $this->assetModel->generateUrl($asset, true, [
            'lead'    => $event->getLead() ? $event->getLead()->getId() : null,
            'channel' => ['form' => $form->getId()],
            ]).'&stream=0';

        if ($messengerMode) {
            $event->setPostSubmitResponse(['download' => $url]);

            return;
        }

        $msg = $message.$this->translator->trans('mautic.asset.asset.submitaction.downloadfile.msg', [
            '%url%' => $url,
        ]);

        $analytics = $this->analyticsHelper->getCode();

        if (!empty($analytics)) {
            $this->assetsHelper->addCustomDeclaration($analytics);
        }

        $event->setPostSubmitResponse(new Response(
            $this->twig->render(
                $this->themeHelper->checkForTwigTemplate('@themes/'.$this->coreParametersHelper->get('theme').'/html/message.html.twig'),
                [
                    'message'  => $msg,
                    'type'     => 'notice',
                    'template' => $this->coreParametersHelper->get('theme'),
                ]
            )
        ));
    }
}
