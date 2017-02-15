<?php

namespace LDAP\Entity;

use Omeka\Entity\User as OmekaUser;

class User extends OmekaUser
{
    protected $username;

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    } 

    public function getUsername()
    {
        return $this->username;
    } 
}