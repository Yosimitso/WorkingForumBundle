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
        $this->persistedEntities[] = $entity;
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
}