# Omeka-S LDAP Module

This plugin allows users to be authenticated against an external LDAP authority. 

Requires php-ldap to be installed on the host server.

## Installation

Firstly, make sure that there is an existing user with an email address that can be authenticated via LDAP with 'Global Administrator' privileges. Once LDAP is enabled you will not be able to log in with existing accounts. 

Copy the plugin source code into `$OMEKA_ROOT/modules`. If you installed via `git clone`, run 'composer install' to install dependencies.

In `$OMEKA_ROOT/modules/LDAP/config`copy `ldap-config.ini.example` to `ldap-config.ini` and add the settings for the ldap server you wish to authenticate against.

Then go to the 'modules' UI in Omeka-S and enable the LDAP module. 

### Gotchas

Omeka-S uses emails as the primary form of user identification, not usernames. At the moment this plugin sends the string before the '@' symbol in the submitted email address as the username to LDAP and then uses the entire submitted email address to locate the user in Omeka-S. 

## Recovery

If something goes wrong deleting `$OMEKA_ROOT/modules/LDAP` or renaming it temporarily to `LDAP2` will disable the plugin and allow you to log in using the default Omeka-S login system.

## Future plans
- Allow both LDAP authenticated and locally authenticated users to work at the same time
- Customisable relationship between email address and LDAP username
