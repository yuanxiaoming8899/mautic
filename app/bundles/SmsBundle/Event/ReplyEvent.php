<?php

namespace Mautic\SmsBundle\Event;

use Mautic\LeadBundle\Entity\Lead;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class ReplyEvent extends Event
{
    private \Mautic\LeadBundle\Entity\Lead $contact;

    /**
     * @var string
     */
    private $message;

    /**
     * @var Response|null
     */
    private $response;

    /**
     * ReplyEvent constructor.
     *
     * @param string $message
     */
    public function __construct(Lead $contact, $message)
    {
        $this->contact = $contact;
        $this->message = $message;
    }

    /**
     * @return Lead
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}
