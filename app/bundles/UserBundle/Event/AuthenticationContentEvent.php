<?php

namespace Mautic\UserBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class AuthenticationContentEvent extends Event
{
    protected \Symfony\Component\HttpFoundation\Request $request;

    /**
     * @var array
     */
    protected $content = [];

    /**
     * @var bool
     */
    protected $postLogout = false;

    public function __construct(Request $request)
    {
        $this->request    = $request;
        $this->postLogout = $request->getSession()->get('post_logout', false);
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return bool
     */
    public function isLogout()
    {
        return $this->postLogout;
    }

    public function addContent($content): void
    {
        $this->content[] = $content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return implode("\n\n", $this->content);
    }
}
