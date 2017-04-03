# Omeka-S LDAP Module

This plugin allows users to be authenticated against an external LDAP authority. 

Requires php-ldap to be installed on the host server.

## Installation

Copy the plugin source code into `$OMEKA_ROOT/modules`. If you installed via `git clone`, run 'composer install' to install dependencies.

In `$OMEKA_ROOT/modules/LDAP/config` copy `ldap-config.ini.example` to `ldap-config.ini` and add the settings for the ldap server you wish to authenticate against.

Then go to the 'modules' UI in Omeka-S and enable the LDAP module. 

*WITHOUT LOGGING OUT* go to the user management section (`/admin/user`) and edit each user you want to be able to log in via LDAP. There is a new 'LDAP Username' field that should be set to exactly the username to be authenticated.

The login page has been replaced with one that uses LDAP. Enter your LDAP username and password and click 'Login'. 

## Recovery

If something goes wrong deleting `$OMEKA_ROOT/modules/LDAP` or renaming it temporarily to `LDAP2` will disable the plugin and allow you to log in using the default Omeka-S login system.

## Future plans
- Allow both LDAP authenticated and locally authenticated users to work at the same time
