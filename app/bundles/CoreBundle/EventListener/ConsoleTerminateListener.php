<?php

namespace Mautic\CoreBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

class ConsoleTerminateListener
{
    private \Psr\Log\LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {
        $statusCode = $event->getExitCode();
        $command    = $event->getCommand();

        if (0 === $statusCode) {
            return;
        }

        if ($statusCode > 255) {
            $statusCode = 255;
            $event->setExitCode($statusCode);
        }

        $this->logger->warning(sprintf(
            'Command `%s` exited with status code %d',
            $command->getName(),
            $statusCode
        ));
    }
}
