<?php

namespace LDAP;

use Omeka\AuthenticationService as OmekaAuthenticationService;
use Zend\Authentication\AuthenticationService as ZendAuthenticationService;

return [
    // Add this section:
    'service_manager' => [
        'aliases' => [            
            ZendAuthenticationService::class => Service\AuthenticationServiceFactory::class
        ],
        'factories' => [
            OmekaAuthenticationService::class => Service\AuthenticationServiceFactory::class
        ],
    ]
];
