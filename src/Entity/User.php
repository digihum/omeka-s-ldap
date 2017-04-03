<?php

namespace LDAP\Entity;

/**
 * @Entity
 * @Table(name="module_ldap_users")
 */
class User
{

    /**
     * @Id
     * @Column(type="integer")
     */
    protected $id;
    
    /**
     * @Column(type="string", length=190, unique=true)
     */
    protected $username;

    public function getId()
    {
        return $this->id;
    }

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