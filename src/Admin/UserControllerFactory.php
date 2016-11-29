<?php
namespace LDAP\Admin;
use Interop\Container\ContainerInterface;
use LDAP\Controller\UserController;
use Zend\ServiceManager\Factory\FactoryInterface;
class UserControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new UserController($services->get('Omeka\EntityManager'));
    }
}
