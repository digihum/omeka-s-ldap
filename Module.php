<?php
namespace LDAP;

require __DIR__ . '/vendor/autoload.php';

use Omeka\Module\AbstractModule;
use Omeka\Entity\Job;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Renderer\PhpRenderer;
use Zend\Mvc\Controller\AbstractController;
use Zend\EventManager\SharedEventManagerInterface;

class Module extends AbstractModule
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function install(ServiceLocatorInterface $services)
    {
        $directory = __DIR__ . '/sql';

        if (! is_dir($directory)) {
            exit('Invalid directory path');
        }

        $files = array();

        foreach (scandir($directory) as $file) {
            if ('.' === $file) continue;
            if ('..' === $file) continue;
            $files[] = $file;
        }

        sort($files);

        $conn = $services->get('Omeka\Connection');

        foreach($files as $file) {
            $sqlScript = file_get_contents($directory . '/' . $file);
            $sqlScriptParts = explode('#----------------#', $sqlScript, 2);
            $conn->exec($sqlScriptParts[0]);
        }
    }
    public function uninstall(ServiceLocatorInterface $services)
    {
        $directory = __DIR__ . '/sql';

        if (! is_dir($directory)) {
            exit('Invalid directory path');
        }

        $files = array();

        foreach (scandir($directory) as $file) {
            if ('.' === $file) continue;
            if ('..' === $file) continue;
            $files[] = $file;
        }

        sort($files);

        $conn = $services->get('Omeka\Connection');

        foreach($files as $file) {
            $sqlScript = file_get_contents($directory . '/' . $file);
            $sqlScriptParts = explode('#----------------#', $sqlScript, 2);
            $conn->exec($sqlScriptParts[1]);
        }
    }
}
