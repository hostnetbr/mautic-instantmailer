<?php

/*
 * @author      Henrique Rodrigues <henrique@hostnet.com.br>
 * @link        https://www.hostnet.com.br
 *
 */

namespace MauticPlugin\MauticMailerBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;
use Mautic\PluginBundle\Entity\Integration;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;

class MailerIntegration extends AbstractIntegration
{

    const MAILER = '/MauticMailerBundle/Helper/MailHelper.php';
    const DEFAULT_MAILER = '/MauticMailerBundle/Helper/DefaultMailHelper.php';
    const SYSTEM_MAILER = '/bundles/EmailBundle/Helper/MailHelper.php';

    public function getName()
    {
        return 'Mailer';
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
                    'label' => 'Send template emails immediately?',
                    'data' => $this->getTemplateMethod(),
                    'attr'  => [
                        'tooltip' => 'Segment emails are not affected by this setting',
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
        $requestData = $this->request->request->all();

        if (!empty($requestData) && isset($requestData['integration_details']['isPublished'])) {
            $published = $requestData['integration_details']['isPublished'];
            $sendImmediately = $requestData['integration_details']['apiKeys']['template_method'];

            if ($published == 1 && $sendImmediately == 1) {
                $this->overrideMailer();
            } else {
                $this->restoreMailer();
            }
        }

        parent::setIntegrationSettings($settings);
    }

    protected function restoreMailer()
    {
        copy(
            $this->pathsHelper->getSystemPath('plugins', true) . self::DEFAULT_MAILER,
            $this->pathsHelper->getSystemPath('app', true) . self::SYSTEM_MAILER
        );
    }

    protected function overrideMailer()
    {
        copy(
            $this->pathsHelper->getSystemPath('plugins', true) . self::MAILER,
            $this->pathsHelper->getSystemPath('app', true) . self::SYSTEM_MAILER
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
                'template'   => 'MauticMailerBundle:Integration:form.html.php',
                'parameters' => [],
            ];
        }

        return parent::getFormNotes($section);
    }
}
