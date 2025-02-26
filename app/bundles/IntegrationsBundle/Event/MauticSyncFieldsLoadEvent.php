<?php

declare(strict_types=1);

namespace Mautic\IntegrationsBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class MauticSyncFieldsLoadEvent extends Event
{
    /**
     * @var array
     */
    private $fields;

    private string $objectName;

    public function __construct(string $objectName, array $fields)
    {
        $this->objectName = $objectName;
        $this->fields     = $fields;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function addField(string $key, string $name): void
    {
        $this->fields[$key] = $name;
    }

    public function getObjectName(): string
    {
        return $this->objectName;
    }
}
