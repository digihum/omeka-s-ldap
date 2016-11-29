<?php

return array(
    'controllers' => array(
        'factories' => array(
            'Omeka\Controller\Admin\User' => 'LDAP\Admin\UserControllerFactory',
        )
    )
);
