<?php

namespace Tests\ObjectivePHP\Package\Doctrine;

use ObjectivePHP\Package\Doctrine\Config\EntityManager;
use PHPUnit\Framework\TestCase;

/**
 * Class EntityManagerTest
 * @package Tests\ObjectivePHP\Package\Doctrine
 */
class EntityManagerTest extends TestCase
{
    public function testAccessors()
    {
        $config = (new EntityManager())
            ->setUser('user')
            ->setPassword('password')
            ->setDbname('dbname')
            ->setDriver('driver')
            ->setHost('host')
            ->setPort(4646)
            ->setEntities('entities');

        $this->assertEquals('user', $config->getUser());
        $this->assertEquals('password', $config->getPassword());
        $this->assertEquals('dbname', $config->getDbname());
        $this->assertEquals('driver', $config->getDriver());
        $this->assertEquals('host', $config->getHost());
        $this->assertEquals(4646, $config->getPort());
        $this->assertEquals('entities', $config->getEntities());
    }
}