<?php

namespace MauticPlugin\HostnetMailerBundle;

use Doctrine\DBAL\Schema\Schema;
use Mautic\PluginBundle\Bundle\PluginBundleBase;
use Mautic\PluginBundle\Entity\Plugin;
use Mautic\CoreBundle\Factory\MauticFactory;

class HostnetMailerBundle extends PluginBundleBase
{
    /**
    *
    * @param Plugin        $plugin
    * @param MauticFactory $factory
    * @param null          $metadata
    * @param Schema        $installedSchema
    *
    * @throws \Exception
    */
    public static function onPluginUpdate(Plugin $plugin, MauticFactory $factory, $metadata = null, Schema $installedSchema = null)
    {
        $integration = $plugin->getIntegrations()->first();

        if ($integration->getIsPublished()) {
            $integration->overrideMailer();
        }
    }
}
