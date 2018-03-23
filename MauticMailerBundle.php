<?php

namespace MauticPlugin\MauticMailerBundle;

use Doctrine\DBAL\Schema\Schema;
use Mautic\PluginBundle\Bundle\PluginBundleBase;
use Mautic\PluginBundle\Entity\Plugin;
use Mautic\CoreBundle\Factory\MauticFactory;

class MauticMailerBundle extends PluginBundleBase
{
    public static function onPluginInstall(
        Plugin $plugin,
        MauticFactory $factory,
        $metadata = null,
        $installedSchema = null
    ) {
        if ($metadata !== null) {
            self::installPluginSchema($metadata, $factory);
        }

        copy(
            $factory->getSystemPath('plugins', true) . '/MauticMailerBundle/Helper/MailHelper.php',
            $factory->getSystemPath('app', true) . '/bundles/EmailBundle/Helper/MailHelper.php'
        );
    }

    public static function onPluginUpdate(
        Plugin $plugin,
        MauticFactory $factory,
        $metadata = null,
        Schema $installedSchema = null
    ) {
        copy(
            $factory->getSystemPath('plugins', true) . '/MauticMailerBundle/Helper/MailHelper.php',
            $factory->getSystemPath('app', true) . '/bundles/EmailBundle/Helper/MailHelper.php'
        );
    }
}
