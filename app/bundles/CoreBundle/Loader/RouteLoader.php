<?php

namespace Mautic\CoreBundle\Loader;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\RouteEvent;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouteCollection;

class RouteLoader extends Loader
{
    private \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher;
    private \Mautic\CoreBundle\Helper\CoreParametersHelper $coreParameters;

    public function __construct(EventDispatcherInterface $dispatcher, CoreParametersHelper $parametersHelper)
    {
        $this->dispatcher     = $dispatcher;
        $this->coreParameters = $parametersHelper;
    }

    /**
     * Load each bundles routing.php file.
     *
     * @param mixed $resource
     * @param null  $type
     *
     * @return RouteCollection
     *
     * @throws \RuntimeException
     */
    public function load($resource, $type = null)
    {
        // Public
        $event = new RouteEvent($this, 'public');
        $this->dispatcher->dispatch($event, CoreEvents::BUILD_ROUTE);
        $collection = $event->getCollection();

        // Force all links to be SSL if the site_url parameter is SSL
        $siteUrl  = $this->coreParameters->get('site_url');
        $forceSSL = false;
        if (!empty($siteUrl)) {
            $parts    = parse_url($siteUrl);
            $forceSSL = (!empty($parts['scheme']) && 'https' == $parts['scheme']);
        }

        if ($forceSSL) {
            $collection->setSchemes('https');
        }

        // Secured area - Default
        $event = new RouteEvent($this);
        $this->dispatcher->dispatch($event, CoreEvents::BUILD_ROUTE);
        $secureCollection = $event->getCollection();

        // OneupUploader (added behind our secure /s)
        $secureCollection->addCollection($this->import('.', 'uploader'));

        // Elfinder file manager
        $collection->addCollection($this->import('@FMElfinderBundle/Resources/config/routing.yaml'));

        // API
        $event = new RouteEvent($this, 'api');
        $this->dispatcher->dispatch($event, CoreEvents::BUILD_ROUTE);
        $apiCollection = $event->getCollection();
        $apiCollection->addPrefix('/api');

        if ($forceSSL) {
            $apiCollection->setSchemes('https');
        }

        $collection->addCollection($apiCollection);

        $secureCollection->addPrefix('/s');
        if ($forceSSL) {
            $secureCollection->setSchemes('https');
        }
        $collection->addCollection($secureCollection);

        // Catch all
        $event = new RouteEvent($this, 'catchall');
        $this->dispatcher->dispatch($event, CoreEvents::BUILD_ROUTE);
        $lastCollection = $event->getCollection();

        if ($forceSSL) {
            $lastCollection->setSchemes('https');
        }

        $collection->addCollection($lastCollection);

        return $collection;
    }

    /**
     * @param mixed $resource
     * @param null  $type
     *
     * @return bool
     */
    public function supports($resource, $type = null)
    {
        return 'mautic' === $type;
    }
}
