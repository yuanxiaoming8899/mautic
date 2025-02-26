<?php

namespace Mautic\PageBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class UntrackableUrlsEvent extends Event
{
    /**
     * @var array
     */
    private $doNotTrack = [
        '{webview_url}',
        '{unsubscribe_url}',
        '{trackable=(.*?)}',
    ];

    /**
     * @var string
     */
    private $content;

    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * set a URL or token to not convert to trackables.
     */
    public function addNonTrackable($url): void
    {
        $this->doNotTrack[] = $url;
    }

    /**
     * Get array of non-trackables.
     *
     * @return array
     */
    public function getDoNotTrackList()
    {
        return $this->doNotTrack;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
