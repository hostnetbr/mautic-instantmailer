<?php

namespace MauticPlugin\MauticMailerBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailSendEvent;
use Mautic\EmailBundle\Helper\MailHelper;
use Mautic\EmailBundle\Entity\Email;

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
        $this->integration = $integration->getIntegrationObject('Mailer');
        $this->mailer = $helper->getSampleMailer();
    }

    /**orm
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            EmailEvents::EMAIL_ON_SEND => ['onEmailSend', 0],
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
                if (!$entity) {
                    $entity = new Email();
                    $entity->setSessionId('new_'.hash('sha1', uniqid(mt_rand())));
                    $entity->setSubject($this->replaceTokens($event->getSubject(), $event->getTokens()));
                    $entity->setCustomHtml($event->getContent(true));
                }

                $temporaryMailer = $this->mailer;
                $temporaryMailer->setEmail($entity);
                $temporaryMailer->setTo(
                    $event->getLead()['email'],
                    "{$event->getLead()['firstname']} {$event->getLead()['lastname']}"
                );

                $temporaryMailer->send();
            }
        }
    }

    public function replaceTokens($message, $tokens)
    {
        return str_replace(array_keys($tokens), $tokens, $message);
    }
}
