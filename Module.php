<?php
namespace LDAP;

require __DIR__ . '/vendor/autoload.php';

use Omeka\Module\AbstractModule;
use Omeka\Entity\Job;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Renderer\PhpRenderer;
use Zend\Mvc\Controller\AbstractController;
use Zend\EventManager\SharedEventManagerInterface;
use DateTime;

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
  
    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        $sharedEventManager->attach(
            'Omeka\Form\UserForm',
            'form.add_elements',
            [$this, 'addUserNameBox']
        );

        $sharedEventManager->attach(
            'Omeka\Form\LoginForm',
            'form.add_elements',
            [$this, 'addLDAPOptions']
        );

        $sharedEventManager->attach(
            'Omeka\Entity\User',
            'entity.update.post',
            [$this, 'userUpdated']
        );

        $sharedEventManager->attach(
            'Omeka\Api\Adapter\UserAdapter',
            'api.hydrate.pre',
            [$this, 'apiHydrate']
        );

         $sharedEventManager->attach(
            'Omeka\Api\Adapter\UserAdapter',
            'api.hydrate.post',
            [$this, 'apiHydrate']
        );
    }

    public function addUserNameBox($event)
    {
        $form = $event->getTarget();
        $form->get('user-information')->add([
            'name' => 'o:username',
            'type' => 'Text',
            'options' => [
                'label' => 'LDAP Username', // @translate
            ],
            'attributes' => [
                'id' => 'username',
                'required' => true,
            ],
        ]);
    }

    public function addLDAPOptions($event)
    {
        $form = $event->getTarget();
        $form->add([
            'name' => 'o:username',
            'type' => 'Text',
            'options' => [
                'label' => 'LDAP Username', // @translate
            ],
            'attributes' => [
                'id' => 'username',
                'required' => true,
            ],
        ]);
    }

    public function apiHydrate($event) {

        $entity = $event->getParam('entity');
        $request = $event->getParam('request');

        $conn = $this->getServiceLocator()->get('Omeka\Connection');
        $dql = "SELECT * FROM module_ldap_users WHERE id=:id";
        $existingMarkers = $conn->fetchAll($dql, [ 'id' => $entity->getId()]);//$query->getSingleResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        $updatedUsername = $request->getContent()['o:username'];

        if ($existingMarkers[0]['username'] != $updatedUsername) {
            $now = new DateTime('NOW');
            $entity->setModified($now);
        }       
        
        $entity->ldap_config = [
            'username' => $updatedUsername
        ];
    }

    public function userUpdated($event)
    {      
        $conn = $this->getServiceLocator()->get('Omeka\Connection');
        $user = $event->getTarget();
        $id = $user->getId();
        $username = $user->ldap_config['username'];

        $existingMarkers = $conn->fetchAll('SELECT * FROM module_ldap_users WHERE id=:id', [ 'id' => $id]);

        $query = '';

        if (count($existingMarkers) > 0) {
            $query = 'UPDATE module_ldap_users SET username = :username WHERE id = :id';
        } else {
            $query = 'INSERT INTO module_ldap_users (id, username) VALUES (:id, :username);';
        }
        
        $conn->executeUpdate($query, [ 'id' => $id, 'username' => $username ]);
        //Place query here, let's say you want all the users that have blue as their favorite color
        // $sql = "SELECT * FROM module_ldap_users WHERE id = :id";

        // //set parameters 
        // //you may set as many parameters as you have on your query
        // $params['id'] = $id;

        // //create the prepared statement, by getting the doctrine connection
        // $stmt = $conn->prepare($sql);
        // $stmt->execute($params);
        // //I used FETCH_COLUMN because I only needed one Column.
        // $meh = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
