<?php

return [
    'name'        => 'Mailer Filter',
    'description' => 'Filter sending of messages on Mautic.',
    'version'     => '1.3.0',
    'author'      => 'Henrique Rodrigues',
    'routes'      => [
    ],
    'services' => [
        'events' => [
            'mautic.mailer.subscriber.form_bundle' => [
                'class'     => 'MauticPlugin\MauticMailerBundle\EventListener\FormSubscriber',
                'arguments' => [
                    'mautic.helper.integration',
                    'mautic.helper.mailer'
                ]
            ]
        ],
        'integrations' => [
            'mautic.integration.mailer' => [
                'class'     => \MauticPlugin\MauticMailerBundle\Integration\MailerIntegration::class,
                'arguments' => [

                ],
            ]
        ]
    ]
];
