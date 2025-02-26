<?php

declare(strict_types=1);

namespace Mautic\IntegrationsBundle\Sync\DAO\Sync\Report;

class RelationDAO
{
    private string $objectName;

    private string $relFieldName;

    private string $relObjectName;

    private string $objectIntegrationId;

    private string $relObjectIntegrationId;

    /**
     * @var int
     */
    private $relObjectInternalId;

    public function __construct(string $objectName, string $relFieldName, string $relObjectName, string $objectIntegrationId, string $relObjectIntegrationId)
    {
        $this->objectName             = $objectName;
        $this->relFieldName           = $relFieldName;
        $this->relObjectName          = $relObjectName;
        $this->objectIntegrationId    = $objectIntegrationId;
        $this->relObjectIntegrationId = $relObjectIntegrationId;
    }

    public function getObjectName(): string
    {
        return $this->objectName;
    }

    public function getRelObjectName(): string
    {
        return $this->relObjectName;
    }

    public function getRelFieldName(): string
    {
        return $this->relFieldName;
    }

    public function getObjectIntegrationId(): string
    {
        return $this->objectIntegrationId;
    }

    public function getRelObjectIntegrationId(): string
    {
        return $this->relObjectIntegrationId;
    }

    public function getRelObjectInternalId(): ?int
    {
        return $this->relObjectInternalId;
    }

    public function setRelObjectInternalId(int $relObjectInternalId): void
    {
        $this->relObjectInternalId = $relObjectInternalId;
    }
}
