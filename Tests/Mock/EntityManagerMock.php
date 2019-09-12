<?php

namespace Yosimitso\WorkingForumBundle\Tests\Mock;

//use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class EntityManagerMock
{
    public $persistedEntities;
    public $flushedEntities;

    public function __construct()
    {
        $this->persistedEntities = [];
        $this->flushedEntities = [];
    }

    public function getRepository()
    {

    }

    public function persist($entity)
    {
        $this->persistedEntities[] = $entity;
    }

    public function flush()
    {
        array_push($this->flushedEntities, ...$this->persistedEntities);
        $this->persistedEntities = [];
    }

    public function getPersistedEntities()
    {
        return $this->persistedEntities;
    }

    public function getFlushedEntities()
    {
        return $this->flushedEntities;
    }
}