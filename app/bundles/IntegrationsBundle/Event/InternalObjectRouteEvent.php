<?php

declare(strict_types=1);

namespace Mautic\IntegrationsBundle\Event;

use Mautic\IntegrationsBundle\Sync\SyncDataExchange\Internal\Object\ObjectInterface;
use Symfony\Contracts\EventDispatcher\Event;

class InternalObjectRouteEvent extends Event
{
    private \Mautic\IntegrationsBundle\Sync\SyncDataExchange\Internal\Object\ObjectInterface $object;

    private int $id;

    /**
     * @var string|null
     */
    private $route;

    public function __construct(ObjectInterface $object, int $id)
    {
        $this->object = $object;
        $this->id     = $id;
    }

    public function getObject(): ObjectInterface
    {
        return $this->object;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(?string $route): void
    {
        $this->route = $route;
    }
}
