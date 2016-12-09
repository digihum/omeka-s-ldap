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
    ]
];
