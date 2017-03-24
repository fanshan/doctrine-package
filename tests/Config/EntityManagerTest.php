<?php
namespace Tests\ObjectivePHP\Package\Config;

use ObjectivePHP\Package\Doctrine\Config\EntityManager;

class EntityManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testDriverSetter()
    {
        $em = new EntityManager('default');
        $em->setDriver('fake-driver');

        $this->assertAttributeEquals(['driver' => 'fake-driver'], 'value', $em);
    }

    public function testHostSetter()
    {
        $em = new EntityManager('default');
        $em->setHost('fake-host');

        $this->assertAttributeEquals(['host' => 'fake-host'], 'value', $em);
    }

    public function testUserSetter()
    {
        $em = new EntityManager('default');
        $em->setUser('fake-user');

        $this->assertAttributeEquals(['user' => 'fake-user'], 'value', $em);
    }

    public function testPasswordSetter()
    {
        $em = new EntityManager('default');
        $em->setPassword('fake-password');

        $this->assertAttributeEquals(['password' => 'fake-password'], 'value', $em);
    }

    public function testDbnameSetter()
    {
        $em = new EntityManager('default');
        $em->setDbname('fake-dbname');

        $this->assertAttributeEquals(['dbname' => 'fake-dbname'], 'value', $em);
    }

    public function testPortSetter()
    {
        $em = new EntityManager('default');
        $em->setPort(3306);

        $this->assertAttributeEquals(['port' => 3306], 'value', $em);
    }

    public function testMappingTypeSetter()
    {
        $em = new EntityManager('default');
        $em->setMappingTypes(['a' => 'b']);

        $this->assertAttributeEquals(['mapping_types' => ['a' =>'b']], 'value', $em);
    }

    public function testAddMappingType()
    {
        $em = new EntityManager('default');
        $em->addMappingType('a', 'b');
        $em->addMappingType('c', 'd');

        $this->assertAttributeEquals(['mapping_types' => ['a' =>'b', 'c' => 'd']], 'value', $em);
    }

    public function testEntitiesLocationsSetter()
    {
        $em = new EntityManager('default');
        $em->setEntitiesLocations(['a', 'b', 'c']);

        $this->assertAttributeEquals(['entities.locations' => ['a', 'b', 'c']], 'value', $em);
    }

    public function testAddEntitiesLocation()
    {
        $em = new EntityManager('default');
        $em->addEntitiesLocation('a');
        $em->addEntitiesLocation('b');

        $this->assertAttributeEquals(['entities.locations' => ['a', 'b']], 'value', $em);
    }
}
