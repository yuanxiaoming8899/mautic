<?php

namespace Mautic\DynamicContentBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\BuildJsEvent;
use Mautic\CoreBundle\Twig\Helper\AssetsHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class BuildJsSubscriber implements EventSubscriberInterface
{
    private \Mautic\CoreBundle\Twig\Helper\AssetsHelper $assetsHelper;

    private \Symfony\Contracts\Translation\TranslatorInterface $translator;

    private \Symfony\Component\HttpFoundation\RequestStack $requestStack;

    private \Symfony\Component\Routing\RouterInterface $router;

    public function __construct(
        AssetsHelper $assetsHelper,
        TranslatorInterface $translator,
        RequestStack $requestStack,
        RouterInterface $router
    ) {
        $this->assetsHelper = $assetsHelper;
        $this->translator   = $translator;
        $this->requestStack = $requestStack;
        $this->router       = $router;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::BUILD_MAUTIC_JS => ['onBuildJs', 200],
        ];
    }

    /**
     * Adds the MauticJS definition and core
     * JS functions for use in Bundles. This
     * must retain top priority of 1000.
     */
    public function onBuildJs(BuildJsEvent $event)
    {
        $dwcUrl = $this->router->generate('mautic_api_dynamicContent_action', ['objectAlias' => 'slotNamePlaceholder'], UrlGeneratorInterface::ABSOLUTE_URL);

        $js = <<<JS
        
           // call variable if doesnt exist
            if (typeof MauticDomain == 'undefined') {
                var MauticDomain = '{$this->requestStack->getCurrentRequest()->getSchemeAndHttpHost()}';
            }            
            if (typeof MauticLang == 'undefined') {
                var MauticLang = {
                     'submittingMessage': "{$this->translator->trans('mautic.form.submission.pleasewait')}"
        };
            }
MauticJS.replaceDynamicContent = function (params) {
    params = params || {};

    var dynamicContentSlots = document.querySelectorAll('.mautic-slot, [data-slot="dwc"]');
    if (dynamicContentSlots.length) {
        MauticJS.iterateCollection(dynamicContentSlots)(function(node, i) {
            var slotName = node.dataset['slotName'];
            if ('undefined' === typeof slotName) {
                slotName = node.dataset['paramSlotName'];
            }
            if ('undefined' === typeof slotName) {
                node.innerHTML = '';
                return;
            }
            var url = '{$dwcUrl}'.replace('slotNamePlaceholder', slotName);

            MauticJS.makeCORSRequest('GET', url, params, function(response, xhr) {
                if (response.content) {
                    var dwcContent = response.content;
                    node.innerHTML = dwcContent;

                    if (response.id && response.sid) {
                        MauticJS.setTrackedContact(response);
                    }

                    // form load library
                    if (dwcContent.search("mauticform_wrapper") > 0) {
                        // if doesn't exist
                        if (typeof MauticSDK == 'undefined') {
                            MauticJS.insertScript('{$this->assetsHelper->getUrl('media/js/mautic-form.js', null, null, true)}');
                            
                            // check initialize form library
                            var fileInterval = setInterval(function() {
                                if (typeof MauticSDK != 'undefined') {
                                    MauticSDK.onLoad(); 
                                    clearInterval(fileInterval); // clear interval
                                 }
                             }, 100); // check every 100ms
                        } else {
                            MauticSDK.onLoad();
                         }
                    }

                    var m;
                    var regEx = /<script[^>]+src="?([^"\s]+)"?\s/g;                    
                    
                    while (m = regEx.exec(dwcContent)) {
                        if ((m[1]).search("/focus/") > 0) {
                            MauticJS.insertScript(m[1]);
                        }
                    }

                    if (dwcContent.search("fr-gatedvideo") > 0) {
                        MauticJS.initGatedVideo();
                    }
                }
            });
        });
    }
};

MauticJS.beforeFirstEventDelivery(MauticJS.replaceDynamicContent);
JS;
        $event->appendJs($js, 'Mautic Dynamic Content');
    }
}
