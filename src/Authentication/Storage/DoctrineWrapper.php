<?php
namespace LDAP\Authentication\Storage;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Zend\Authentication\Storage\StorageInterface;

/**
 * Auth storage wrapper for doctrine objects.
 *
 * Stores the ID instead of the full object, translates between ID and object
 * automatically on read/write.
 */
class DoctrineWrapper implements StorageInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
    * @var TrueEntityRepository
    */
    protected $true_repository;

    /**
     * Create the wrapper around the given storage method, looking up users
     * from the given repository.
     *
     * @param StorageInterface $storage "Base" storage class
     * @param EntityRepository $repository Repository storing Users
     */
    public function __construct(StorageInterface $storage, EntityRepository $repository, EntityRepository $true_repository)
    {
        $this->setStorage($storage);
        $this->setRepositories($repository, $true_repository);
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        if ($this->storage->isEmpty()) {
            return true;
        }
        if (null === $this->read()) {
            // An identity may exist in a cookie but not in the database.
            return true;
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function read()
    {
        $identity = $this->storage->read();
        if ($identity) {
            try {
                $result = $this->true_repository->find($identity); 
                return $result;
            } catch (DBALException $e) {
                // The user table does not exist.
                return null;
            }
        }
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function write($identity)
    {
        $omekaUserPointer = $this->repository->findOneBy([ 'username' => $identity ]);
        $omekaUser = $this->true_repository->findOneBy([ 'id' => $omekaUserPointer->getId() ]);
        $this->storage->write($omekaUser->getId());
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->storage->clear();
    }

    /**
     * Set the base storage class to wrap.
     *
     * @param StorageInterface $storage
     */
    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Get the storage class being wrapped.
     *
     * @return StorageInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Set the repository for looking up User objects.
     *
     * @param EntityRepository $repository
     */
    public function setRepositories(EntityRepository $repository, EntityRepository $true_repository)
    {
        $this->repository = $repository;
        $this->true_repository = $true_repository;
    }

    /**
     * Get the repository for looking up User objects.
     *
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }
}
