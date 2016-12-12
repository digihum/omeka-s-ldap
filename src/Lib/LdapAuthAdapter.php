<?php

namespace LDAP\Lib;

use Zend\Authentication\Adapter\Ldap as AuthAdapter;
use Zend\Authentication\Result as AuthResult;

/**
 * The Authorization Adapter for the LDAP plugin.
 *
 * @package LDAP
 * @author  Dave Widmer <dwidmer@bgsu.edu>
 */
class LdapAuthAdapter extends AuthAdapter
{
	/**
	 * Attempts to authenticate the user.
	 *
	 * @return Zend_Auth_Result   The authorization result
	 */
	public function authenticate()
	{
            $originalIdentity = $this->getIdentity();
            $this->setIdentity(strtok($originalIdentity, '@'));
            //var_dump($this->getUsername()); die;
	    $result = parent::authenticate();
            if ($result->isValid()) 
            {
                return new AuthResult($result->getCode(), $originalIdentity, $result->getMessages());
            }
            error_log(print_r($result->getIdentity(), False));
            return $result;
	}
	/**
	 * A hook that handles when a user is not found in the omeka database.
	 *
	 * This function just gives back an invalid login message. You have to match
	 * the user accounts from you LDAP manually.
	 * 
	 * Override this function if you want to create a new Omeka user if they are
	 * authenticated with the LDAP.
	 *
	 * @return Zend_Auth_Result
	 */
	protected function hookUserNotFound()
	{
		$messages = array();
		$message[] = 'Login information incorrect. Please try again.';
		return new AuthResult(AuthResult::FAILURE_IDENTITY_NOT_FOUND, $this->getUsername(), $message);
	}
}
