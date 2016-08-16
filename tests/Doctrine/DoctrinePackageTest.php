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

    public function testPackageIsCallable()
    {
        $package = new DoctrinePackage();
        $this->assertTrue(is_callable($package));
    }

    public function testBuildEntityManagers()
    {
        $package = new DoctrinePackage();

        $app = $this->getMockBuilder(AbstractApplication::class)->setMethods(['getConfig'])->getMockForAbstractClass();
        $config = $this->getMockBuilder(Config::class)->getMock();

        $config->expects($this->once())->method('subset')->willReturn(
            ['test' => [
                'entities.locations' => __DIR__,
                'driver' => 'pdo_sqlite',
                'memory' => true,
                'mapping_types' => [
                    'enum' => 'string',
                    'test' => IntegerType::class
                ]
            ]]
        );
        $app->expects($this->once())->method('getConfig')->willReturn($config);

        $package->buildEntityManagers($app);

        $this->assertTrue($app->getServicesFactory()->has('doctrine.em.test'));

        /** @var EntityManager $em */
        $em = $app->getServicesFactory()->get('doctrine.em.test');
        $this->assertEquals('string', $em->getConnection()->getDatabasePlatform()->getDoctrineTypeMapping('enum'));
        $this->assertEquals('test', $em->getConnection()->getDatabasePlatform()->getDoctrineTypeMapping('test'));
    }
}
