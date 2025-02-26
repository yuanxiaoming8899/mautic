<?php

declare(strict_types=1);

namespace Mautic\IntegrationsBundle\Sync\DAO\Sync\Request;

class ObjectDAO
{
    private string $object;

    /**
     * Date/time based on last synced date for the object or the start date/time fed through the command's arguments.
     * This value does not change between iterations.
     */
    private ?\DateTimeInterface $fromDateTime;

    /**
     * Date/Time the sync started.
     */
    private ?\DateTimeInterface $toDateTime;

    private ?\DateTimeInterface $objectLastSyncDateTime;

    /**
     * @var string[]
     */
    private $fields = [];

    /**
     * @var string[]
     */
    private $requiredFields = [];

    public function __construct(
        string $object,
        ?\DateTimeInterface $fromDateTime = null,
        ?\DateTimeInterface $toDateTime = null,
        ?\DateTimeInterface $objectLastSyncDateTime = null
    ) {
        $this->object                 = $object;
        $this->fromDateTime           = $fromDateTime;
        $this->toDateTime             = $toDateTime;
        $this->objectLastSyncDateTime = $objectLastSyncDateTime;
    }

    public function getObject(): string
    {
        return $this->object;
    }

    /**
     * @return self
     */
    public function addField(string $field)
    {
        $this->fields[] = $field;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function setRequiredFields(array $fields): void
    {
        $this->requiredFields = $fields;
    }

    /**
     * @return string[]
     */
    public function getRequiredFields(): array
    {
        return $this->requiredFields;
    }

    public function getFromDateTime(): ?\DateTimeInterface
    {
        return $this->fromDateTime;
    }

    public function getToDateTime(): ?\DateTimeInterface
    {
        return $this->toDateTime;
    }

    public function getObjectLastSyncDateTime(): ?\DateTimeInterface
    {
        return $this->objectLastSyncDateTime;
    }
}
