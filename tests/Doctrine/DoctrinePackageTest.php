<?php

namespace Tests\ObjectivePHP\Package\Doctrine;

use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\ORM\EntityManager;
use ObjectivePHP\Application\AbstractApplication;
use ObjectivePHP\Config\Config;
use ObjectivePHP\Package\Doctrine\DoctrinePackage;;

class DoctrinePackageTest extends \PHPUnit_Framework_TestCase
{

    protected $app;
    
    public function testPackageIsCallable()
    {
        $package = new DoctrinePackage();
        $this->assertTrue(is_callable($package));
    }

    public function testBuildEntityManagers()
    {
        $this->buildConfig(
            ['test' => [
                'entities.locations' => __DIR__,
                'driver' => 'pdo_sqlite',
                'memory' => true,
                'mapping_types' => [
                    'enum' => 'string',
                    'test' => IntegerType::class
                ],
            ]]
        );


        /** @var EntityManager $em */
        $em = $this->app->getServicesFactory()->get('doctrine.em.test');
        $this->assertEquals('string', $em->getConnection()->getDatabasePlatform()->getDoctrineTypeMapping('enum'));
        $this->assertEquals('test', $em->getConnection()->getDatabasePlatform()->getDoctrineTypeMapping('test'));
        $this->assertAttributeInstanceOf('Doctrine\Common\Annotations\SimpleAnnotationReader', 'delegate', $em->getConfiguration()->getMetaDataDriverImpl()->getReader());
    }

    public function testBuildEntityManagersWithSimpleAnnotationReader()
    {
        $this->buildConfig(
            ['test' => [
                'entities.locations' => __DIR__,
                'driver' => 'pdo_sqlite',
                'memory' => true,
                'use_simple_annotation_reader' => true
            ]]
        );


        /** @var EntityManager $em */
        $em = $this->app->getServicesFactory()->get('doctrine.em.test');
        $this->assertAttributeInstanceOf('Doctrine\Common\Annotations\SimpleAnnotationReader', 'delegate', $em->getConfiguration()->getMetaDataDriverImpl()->getReader());

    }

    public function testBuildEntityManagersWithAnnotationReader()
    {
        $this->buildConfig(
            ['test' => [
                'entities.locations' => __DIR__,
                'driver' => 'pdo_sqlite',
                'memory' => true,
                'use_simple_annotation_reader' => false
            ]]
        );

        /** @var EntityManager $em */
        $em = $this->app->getServicesFactory()->get('doctrine.em.test');
        $this->assertAttributeInstanceOf('Doctrine\Common\Annotations\AnnotationReader', 'delegate', $em->getConfiguration()->getMetaDataDriverImpl()->getReader());

    }

    protected function buildConfig(array $configArray) 
    {
        $package = new DoctrinePackage();

        $this->app = $this->getMockBuilder(AbstractApplication::class)->setMethods(['getConfig'])->getMockForAbstractClass();
        $config = $this->getMockBuilder(Config::class)->getMock();

        $config->expects($this->once())->method('subset')->willReturn($configArray);
        $this->app->expects($this->once())->method('getConfig')->willReturn($config);
        
        $package->buildEntityManagers($this->app);

        $this->assertTrue($this->app->getServicesFactory()->has('doctrine.em.test'));        

    }

}
