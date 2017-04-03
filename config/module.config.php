<?php

namespace LDAP;

use Omeka\AuthenticationService as OmekaAuthenticationService;
use Zend\Authentication\AuthenticationService as ZendAuthenticationService;

return [
    // Add this section:
    'service_manager' => [
        'factories' => [
            'Omeka\AuthenticationService' => 'LDAP\Service\AuthenticationServiceFactory'
        ]
    ],

    'controllers' => [
        'factories' => [
            'Omeka\Controller\Login' => 'LDAP\Service\Controller\LoginControllerFactory'
        ]
    ],

    'view_manager' => [
        'template_path_stack' => [
            OMEKA_PATH . '/modules/LDAP/view',
        ],
    ]
];
