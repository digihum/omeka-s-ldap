<?php
namespace LDAP\Service;

use Omeka\Authentication\Adapter\KeyAdapter;
use Omeka\Authentication\Adapter\PasswordAdapter;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\Callback;
use Zend\Authentication\Storage\NonPersistent;
use Zend\Authentication\Storage\Session;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

use Zend\Config\Config;
use Zend\Config\Reader\Ini as ConfigReader;
use Zend\Authentication\Adapter\Ldap as AuthAdapter;

use LDAP\Lib\LdapAuthAdapter;
use LDAP\Authentication\Storage\DoctrineWrapper;

/**
 * Authentication service factory.
 */
class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * Create the authentication service.
     *
     * @return ApiManager
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $entityManager = $serviceLocator->get('Omeka\EntityManager');
        $status = $serviceLocator->get('Omeka\Status');

        // Skip auth retrieval entirely if we're installing or migrating.
        if (!$status->isInstalled() ||
            ($status->needsVersionUpdate() && $status->needsMigration())
        ) {
            $storage = new NonPersistent;
            $adapter = new Callback(function () { return null; });
        } else {
            $userRepository = $entityManager->getRepository('Omeka\Entity\User');
            if ($status->isApiRequest()) {
                // Authenticate using key for API requests.
                $keyRepository = $entityManager->getRepository('Omeka\Entity\ApiKey');
                $storage = new DoctrineWrapper(new NonPersistent, $userRepository);
                $adapter = new KeyAdapter($keyRepository, $entityManager);
            } else {
                // Authenticate using ldap for all other requests
                $storage = new DoctrineWrapper(new Session, $userRepository);
                //$adapter = new PasswordAdapter($userRepository);
                
                $configReader = new ConfigReader();
                $configData = $configReader->fromFile(__DIR__ . '/../../config/ldap-config.ini');
                $config = new Config($configData, true);

                $log_path = $config->production->ldap->log_path;
                $options = $config->production->ldap->toArray();
                unset($options['log_path']);

                $adapter = new LdapAuthAdapter($options);
            }
        }

        $authService = new AuthenticationService($storage, $adapter);
        return $authService;
    }
}
