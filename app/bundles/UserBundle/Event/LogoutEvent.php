<?php

namespace Mautic\UserBundle\Event;

use Mautic\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class LogoutEvent extends Event
{
    private \Mautic\UserBundle\Entity\User $user;

    /**
     * @var array
     */
    private $session = [];

    private \Symfony\Component\HttpFoundation\Request $request;

    public function __construct(User $user, Request $request)
    {
        $this->user    = $user;
        $this->request = $request;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add value to session after it's been cleared.
     */
    public function setPostSessionItem($key, $value): void
    {
        $this->session[$key] = $value;
    }

    /**
     * Get session items to be added after session has been cleared.
     *
     * @return array
     */
    public function getPostSessionItems()
    {
        return $this->session;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
