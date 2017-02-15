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

    // 'view_manager' => [
    //     'template_map' => [
    //         'omeka/login/login' => __DIR__ . '/../view-shared/omeka/login/login.phtml',
    //     ],
    // ],
    // 'entity_manager' => [
    //     'mapping_classes_paths' => [
    //         OMEKA_PATH . '/modules/LDAP/src/Entity',
    //     ],
    //     'resource_discriminator_map' => [
    //         'Omeka\Entity\User' => 'LDAP\Entity\User',
    //     ]
    // ],
];
