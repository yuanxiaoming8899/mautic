<?php

namespace Mautic\EmailBundle\EventListener;

use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailSendEvent;
use Mautic\EmailBundle\Event\MonitoredEmailEvent;
use Mautic\EmailBundle\Event\ParseEmailEvent;
use Mautic\EmailBundle\MonitoredEmail\Processor\FeedbackLoop;
use Mautic\EmailBundle\MonitoredEmail\Processor\Unsubscribe;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProcessUnsubscribeSubscriber implements EventSubscriberInterface
{
    public const BUNDLE     = 'EmailBundle';
    public const FOLDER_KEY = 'unsubscribes';

    private \Mautic\EmailBundle\MonitoredEmail\Processor\Unsubscribe $unsubscriber;

    private \Mautic\EmailBundle\MonitoredEmail\Processor\FeedbackLoop $looper;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            EmailEvents::MONITORED_EMAIL_CONFIG => ['onEmailConfig', 0],
            EmailEvents::EMAIL_PARSE            => ['onEmailParse', 0],
            EmailEvents::EMAIL_ON_SEND          => ['onEmailSend', 0],
        ];
    }

    public function __construct(Unsubscribe $unsubscriber, FeedbackLoop $looper)
    {
        $this->unsubscriber = $unsubscriber;
        $this->looper       = $looper;
    }

    public function onEmailConfig(MonitoredEmailEvent $event): void
    {
        $event->addFolder(self::BUNDLE, self::FOLDER_KEY, 'mautic.email.config.monitored_email.unsubscribe_folder');
    }

    public function onEmailParse(ParseEmailEvent $event): void
    {
        if ($event->isApplicable(self::BUNDLE, self::FOLDER_KEY)) {
            // Process the messages
            $messages = $event->getMessages();
            foreach ($messages as $message) {
                if (!$this->unsubscriber->process($message)) {
                    $this->looper->process($message);
                }
            }
        }
    }

    /**
     * Add an unsubscribe email to the List-Unsubscribe header if applicable.
     */
    public function onEmailSend(EmailSendEvent $event): void
    {
        $helper = $event->getHelper();
        if ($helper && $unsubscribeEmail = $helper->generateUnsubscribeEmail()) {
            $headers          = $event->getTextHeaders();
            $existing         = (isset($headers['List-Unsubscribe'])) ? $headers['List-Unsubscribe'] : '';
            $unsubscribeEmail = "<mailto:$unsubscribeEmail>";
            if ($existing) {
                if (false === strpos($existing, $unsubscribeEmail)) {
                    $updatedHeader = $unsubscribeEmail.', '.$existing;
                } else {
                    $updatedHeader = $existing;
                }
            } else {
                $updatedHeader = $unsubscribeEmail;
            }

            $event->addTextHeader('List-Unsubscribe', $updatedHeader);
        }
    }
}
