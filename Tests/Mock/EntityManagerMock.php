<?php

namespace Yosimitso\WorkingForumBundle\Tests\Mock;

//use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class EntityManagerMock
{
    public $persistedEntities;
    public $removedEntities;
    public $flushedEntities;

    public function __construct()
    {
        $this->persistedEntities = [];
        $this->flushedEntities = [];
        $this->removedEntities = [];
    }

    public function getRepository()
    {

    }

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

    public function remove($entity)
    {
        $this->removedEntities[] = $entity;
    }

    public function flush() // WARNING : ARRAY PERSISTED ENTITIES AND REMOVED ENTITIES AREN'T EMPTY AFTER FLUSH
    {
        array_push($this->flushedEntities, ...$this->persistedEntities, ...$this->removedEntities);
    }

    public function getPersistedEntities()
    {
        return $this->persistedEntities;
    }

    public function getRemovedEntities()
    {
        return $this->removedEntities;
    }

    public function getFlushedEntities()
    {
        return $this->flushedEntities;
    }

    public function getFlushedEntity($className)
    {
        foreach ($this->flushedEntities as $entity) {
            if (get_class($entity) === $className) {
                return $entity;
            }
        }

        throw new \Exception('Class '.$className.' not found in flushed entities');
    }

    public function getListFlushedEntities()
    {
        $list = [];
        foreach ($this->flushedEntities as $entity) {
            $list[] = get_class($entity);
        }

        return $list;
    }
}