<?php

return array(
    'service_manager' => array(
        'factories' => array(
            'Omeka\AuthenticationService' => 'LDAP\Service\AuthenticationServiceFactory'
        )
    )
);
