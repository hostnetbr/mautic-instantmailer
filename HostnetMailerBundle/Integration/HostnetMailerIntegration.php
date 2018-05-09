<?php

/*
 * @author      Henrique Rodrigues <henrique@hostnet.com.br>
 * @link        https://www.hostnet.com.br
 *
 */

namespace MauticPlugin\HostnetMailerBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;
use Mautic\PluginBundle\Entity\Integration;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;

class HostnetMailerIntegration extends AbstractIntegration
{
    const MAILER = '/HostnetMailerBundle/Helper/MailHelper.php';
    const DEFAULT_MAILER = '/HostnetMailerBundle/Helper/DefaultMailHelper.php';
    const SYSTEM_MAILER = '/bundles/EmailBundle/Helper/MailHelper.php';

    const SPOOL = '/HostnetMailerBundle/Helper/FileSpool.php';
    const DEFAULT_SPOOL = '/HostnetMailerBundle/Helper/DefaultFileSpool.php';
    const VENDOR_SPOOL  = '/swiftmailer/swiftmailer/lib/classes/Swift/FileSpool.php';

    const QUEUE = '/HostnetMailerBundle/Helper/ProcessEmailQueueCommand.php';
    const DEFAULT_QUEUE = '/HostnetMailerBundle/Helper/DefaultProcessEmailQueueCommand.php';
    const SYSTEM_QUEUE  = '/bundles/EmailBundle/Command/ProcessEmailQueueCommand.php';

    public function getName()
    {
        return 'HostnetMailer';
    }

    public function getDisplayName()
    {
        return 'Mailer Configuration';
    }

    /**
     * Return's authentication method such as oauth2, oauth1a, key, etc.
     *
     * @return string
     */
    public function getAuthenticationType()
    {
        return 'none';
    }

    /**
     * Return array of key => label elements that will be converted to inputs to
     * obtain from the user.
     *
     * @return array
     */
    public function getRequiredKeyFields()
    {
        die($this->pathsHelper->getSystemPath('vendor', true));
        return [
        ];
    }

    /**
     * @param FormBuilder|Form $builder
     * @param array            $data
     * @param string           $formArea
     */
    public function appendToForm(&$builder, $data, $formArea)
    {
        if ($formArea === 'keys') {
            $builder->add(
                'template_method',
                'yesno_button_group',
                [
                    'label' => 'mautic.integration.mailer.config_label',
                    'data' => $this->getTemplateMethod(),
                    'attr'  => [
                        'tooltip' => 'mautic.integration.mailer.config_tooltip',
                    ],
                ]
            );
        }
    }

    public function getTemplateMethod()
    {
        $featureSettings = $this->getKeys();

        return isset($featureSettings['template_method'])
            ? $featureSettings['template_method']
            : false;
    }

    public function setIntegrationSettings(Integration $settings)
    {
        if (empty($this->request)) {
            $requestData = $this->request;
        } else {
            $request = $this->request->request;
            $requestData = empty($request) ? $request : $request->all();
        }

        if (!empty($requestData)
            && isset($requestData['integration_details']['isPublished'])
        ) {
            $published = $requestData['integration_details']['isPublished'];
            $sendImmediately = $requestData['integration_details']['apiKeys']['template_method'];

            if ($published == '1' && $sendImmediately == '1') {
                $this->overrideMailer();
            } else {
                $this->restoreMailer();
            }
        }

        parent::setIntegrationSettings($settings);
    }

    public function restoreMailer()
    {
        // Restore Mautic MailHelper
        copy(
            $this->pathsHelper->getSystemPath('plugins', true) . self::DEFAULT_MAILER,
            $this->pathsHelper->getSystemPath('app', true) . self::SYSTEM_MAILER
        );

        // Restore Mautic queue processor
        copy(
            $this->pathsHelper->getSystemPath('plugins', true) . self::DEFAULT_QUEUE,
            $this->pathsHelper->getSystemPath('app', true) . self::SYSTEM_QUEUE
        );

        // Restore SwiftMailer spool handler
        copy(
            $this->pathsHelper->getSystemPath('plugins', true) . self::DEFAULT_SPOOL,
            $this->pathsHelper->getSystemPath('vendor', true) . self::SYSTEM_SPOOL
        );
    }

    public function overrideMailer()
    {
        // Override Mautic MailHelper
        copy(
            $this->pathsHelper->getSystemPath('plugins', true) . self::MAILER,
            $this->pathsHelper->getSystemPath('app', true) . self::SYSTEM_MAILER
        );

        // Override Mautic queue processor
        copy(
            $this->pathsHelper->getSystemPath('plugins', true) . self::QUEUE,
            $this->pathsHelper->getSystemPath('app', true) . self::SYSTEM_QUEUE
        );

        // Override SwiftMailer spool handler
        copy(
            $this->pathsHelper->getSystemPath('plugins', true) . self::SPOOL,
            $this->pathsHelper->getSystemPath('vendor', true) . self::SYSTEM_SPOOL
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param $section
     *
     * @return string|array
     */
    public function getFormNotes($section)
    {
        if ('custom' === $section) {
            return [
                'template'   => 'HostnetMailerBundle:Integration:form.html.php',
                'parameters' => [],
            ];
        }

        return parent::getFormNotes($section);
    }
}
