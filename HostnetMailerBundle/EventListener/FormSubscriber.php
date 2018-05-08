<?php

namespace MauticPlugin\HostnetMailerBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\PluginBundle\Helper\IntegrationHelper;
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
        IntegrationHelper $integration
    ) {
        $this->integration = $integration->getIntegrationObject('HostnetMailer');
    }

    /**orm
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::POST_UPGRADE => ['onPostUpgrade', 0],
        ];
    }

    /**
     * Replaces de default mailer on system update
     *
     * @param UpgradeEvent $event
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
}
