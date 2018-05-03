<?php

namespace MauticPlugin\HostnetMailerBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailSendEvent;
use Mautic\EmailBundle\Helper\MailHelper;
use Mautic\EmailBundle\Entity\Email;
use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\UpgradeEvent;

/**
 * Class FormSubscriber
 *
 * @author Henrique Rodrigues <henrique@hostnet.com.br>
 *
 * @link https://www.hostnet.com.br
 *
 */
class FormSubscriber extends CommonSubscriber
{
    protected $integration;

    public function __construct(
        IntegrationHelper $integration,
        MailHelper $helper
    ) {
        $this->integration = $integration->getIntegrationObject('HostnetMailer');
        $this->mailer = $helper->getSampleMailer();
    }

    /**orm
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            EmailEvents::EMAIL_ON_SEND => ['onEmailSend', 0],
            CoreEvents::POST_UPGRADE => ['onPostUpgrade', 0],
        ];
    }

    /**
     * Captures the send event and defines if the message goes to the queue or is sent immediately
     *
     * @param GetResponseEvent $event
     * @return void
     */
    public function onEmailSend(EmailSendEvent $event)
    {
        if ($this->integration) {
            $published = $this->integration->getIntegrationSettings()
                ->getIsPublished();

            $entity = $event->getEmail();

            $emailType = $entity
                ? $entity->getEmailType()
                : 'template';

            if ($published
                && $this->integration->getTemplateMethod()
                && $emailType === 'template'
            ) {
                $entity = new Email();
                $entity->setSessionId('new_'.hash('sha1', uniqid(mt_rand())));
                $entity->setSubject($this->replaceTokens($event->getSubject(), $event->getTokens()));
                $entity->setCustomHtml($event->getContent(true));

                $temporaryMailer = $this->mailer;
                $temporaryMailer->setEmail($entity);
                $temporaryMailer->setTo(
                    $event->getHelper()->message->getTo()
                );
                $temporaryMailer->send();
            }
        }
    }

    /**
     * Replaces de default mailer on system update
     *
     * @param GetResponseEvent $event
     * @return void
     */
    public function onPostUpgrade(UpgradeEvent $event)
    {
        if (!$this->integration) {
            return false;
        }

        $published = $this->integration->getIntegrationSettings()
                ->getIsPublished();

        if ($published && $this->integration->getTemplateMethod()) {
            $this->integration->overrideMailer();
        }
    }

    public function replaceTokens($message, $tokens)
    {
        return str_replace(array_keys($tokens), $tokens, $message);
    }
}
