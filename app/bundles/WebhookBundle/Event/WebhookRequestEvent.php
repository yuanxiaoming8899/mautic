<?php

declare(strict_types=1);

namespace Mautic\WebhookBundle\Event;

use Mautic\LeadBundle\Entity\Lead;
use Symfony\Contracts\EventDispatcher\Event;

class WebhookRequestEvent extends Event
{
    private string $url;

    private array $headers;

    private array $payload;

    private \Mautic\LeadBundle\Entity\Lead $contact;

    public function __construct(Lead $contact, string $url, array $headers, array $payload)
    {
        $this->url     = $url;
        $this->headers = $headers;
        $this->payload = $payload;
        $this->contact = $contact;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    public function getContact(): Lead
    {
        return $this->contact;
    }
}
