<?php

namespace Yosimitso\WorkingForumBundle\Tests\Mock;


class EntityManagerMock
{
    /**
     * Logs persisted entities during the workflow
     * @var array
     */
    public $persistedEntities;
    /**
     * Logs removed entities during the workflow
     * @var array
     */
    public $removedEntities;
    /**
     * Logs flushed entities
     * @var array
     */
    public $flushedEntities;

    /**
     * EntityManagerMock constructor.
     */
    public function __construct()
    {
        $this->persistedEntities = [];
        $this->flushedEntities = [];
        $this->removedEntities = [];
    }

    /**
     * Method to mock
     */
    public function getRepository()
    {
        return $this;
    }

    /**
     * Persist an entity, this method inserts or updates the entity
     * @param $entity
     */
    public function persist($entity)
    {
        foreach ($this->persistedEntities as $index => $entityPersisted) {
            if (spl_object_id($entityPersisted) === spl_object_id($entity)) { // UPDATE OBJECT IF IT EXISTS
                $this->persistedEntities[$index] = $entity;
                return;
            }
        }

        $this->persistedEntities[] = $entity; // ENTITY WASN'T PERSISTED YET
    }

    /**
     * @param $entity
     */
    public function remove($entity)
    {
        $this->removedEntities[] = $entity;
    }

    /**
     *
     */
    public function flush() // WARNING : ARRAY PERSISTED ENTITIES AND REMOVED ENTITIES AREN'T EMPTY AFTER FLUSH
    {
        array_push($this->flushedEntities, ...$this->persistedEntities, ...$this->removedEntities);
    }

    /**
     * Returns if a persisted entity has been flushed
     * @param $entity
     * @return bool
     */
    public function hasBeenFlushed($entity)
    {
        $splId = null;
        foreach ($this->persistedEntities as $persistedEntity) {
            if (spl_object_id($entity) === spl_object_id($persistedEntity)) {
                $splId = spl_object_id($entity);
            }
        }

        if (is_null($splId)) {
            throw new \Exception('The entity '.get_class($entity).' must be persisted before testing if it has been flushed');
        }

        foreach ($this->flushedEntities as $flushedEntity) {
            if (spl_object_id($entity) === spl_object_id($flushedEntity)) {
                return true;
            }
        }

        return false;       // HASN'T BEEN FOUND 
    }

    /**
     * Get all persisted entities
     * @return array
     */
    public function getPersistedEntities()
    {
        return $this->persistedEntities;
    }

    /**
     * Get all removed entities
     * @return array
     */
    public function getRemovedEntities()
    {
        return $this->removedEntities;
    }

    /**
     * Get all flushed entities
     * @return array
     */
    public function getFlushedEntities()
    {
        return $this->flushedEntities;
    }

    /**
     * Get the first persisted entity by its class
     * @param $className
     * @return mixed
     * @throws \Exception
     */
    public function getPersistedEntity($className)
    {
        foreach ($this->persistedEntities as $entity) {
            if (get_class($entity) === $className) {
                return $entity;
            }
        }

        throw new \Exception('Class '.$className.' not found in persisted entities');
    }

    /**
     * Get the first flushed entity by its class
     * @param $className
     * @return mixed
     * @throws \Exception
     */
    public function getFlushedEntity($className)
    {
        foreach ($this->flushedEntities as $entity) {
            if (get_class($entity) === $className) {
                return $entity;
            }
        }

        throw new \Exception('Class '.$className.' not found in flushed entities');
    }
}
