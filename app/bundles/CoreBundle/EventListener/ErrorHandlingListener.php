<?php

namespace Mautic\CoreBundle\EventListener;

use Mautic\CoreBundle\ErrorHandler\ErrorHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ErrorHandlingListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 2047],
        ];
    }

    /**
     * ErrorHandlingListener constructor.
     */
    public function __construct(LoggerInterface $logger, LoggerInterface $mainLogger, LoggerInterface $debugLogger = null)
    {
        ErrorHandler::getHandler()
            ->setLogger($logger)
            ->setMainLogger($mainLogger)
            ->setDebugLogger($debugLogger);
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        // Do nothing.  Just want symfony to call the class to set the error handling functions
    }
}
