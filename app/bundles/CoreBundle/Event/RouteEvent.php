<?php

namespace Mautic\CoreBundle\Event;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Contracts\EventDispatcher\Event;

class RouteEvent extends Event
{
    protected \Symfony\Component\Config\Loader\Loader $loader;

    protected \Symfony\Component\Routing\RouteCollection $collection;

    /**
     * @var string
     */
    protected $type;

    public function __construct(Loader $loader, $type = 'main')
    {
        $this->loader     = $loader;
        $this->collection = new RouteCollection();
        $this->type       = $type;
    }

    /**
     * @param string $path
     */
    public function addRoutes($path): void
    {
        $this->collection->addCollection($this->loader->import($path));
    }

    /**
     * @return RouteCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
