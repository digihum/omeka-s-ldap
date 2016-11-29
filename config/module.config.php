<?php

namespace LDAP;

use Omeka\AuthenticationService as OmekaAuthenticationService;
use Zend\Authentication\AuthenticationService as ZendAuthenticationService;

return [
    // Add this section:
    'service_manager' => [
        'aliases' => [            
            ZendAuthenticationService::class => LDAP\Service\AuthenticationServiceFactory::class
        ],
        'factories' => [
            OmekaAuthenticationService::class => LDAP\Service\AuthenticationServiceFactory::class
        ],
    ]
];
