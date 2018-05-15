<?php
/**
 * This file is part of the Objective PHP project
 *
 * More info about Objective PHP on www.objective-php.org
 *
 * @license http://opensource.org/licenses/GPL-3.0 GNU GPL License 3.0
 */

namespace Tests\ObjectivePHP\Package\Doctrine;

use ObjectivePHP\Application\ApplicationInterface;
use ObjectivePHP\Application\Workflow\WorkflowEvent;
use ObjectivePHP\Config\Config;
use ObjectivePHP\Package\Doctrine\DoctrinePackage;
use ObjectivePHP\Package\Doctrine\Config\EntityManager;
use ObjectivePHP\ServicesFactory\ServicesFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class DoctrinePackageTest
 * @package Tests\ObjectivePHP\Package\Doctrine
 */
class DoctrinePackageTest extends TestCase
{
    public function testGetConfig()
    {
        $this->assertEquals(
            (new DoctrinePackage())->getConfig(),
            new Config(new EntityManager())
        );
    }

    public function testPackageCanInit()
    {
        $package = new DoctrinePackage();
        $this->assertTrue(method_exists($package, 'onPackagesInit'));
    }

    public function testOnPackagesInit()
    {
        $config = (new Config(new EntityManager()))
            ->set(
                EntityManager::KEY,
                [
                    "default" => [
                        "driver" => "pdo_sqlite",
                        "host" => "test_db",
                        "port" => "3306",
                        "dbname" => "testDb",
                        "user" => "user",
                        "password" => "password",
                        "entities" => "vendor/fei/test-common/src/Entity"
                    ]
                ]
            );

        $servicesFactory = $this->getMockBuilder(ServicesFactory::class)
            ->setMethods(['registerService'])
            ->getMock();
        $servicesFactory->expects($this->exactly(2))
            ->method('registerService');


        $app = $this->createMock(ApplicationInterface::class);
        $app->method('getConfig')->willReturn($config);
        $app->method('getServicesFactory')->willReturn($servicesFactory);

        $package = $this->getMockBuilder(DoctrinePackage::class)
            ->setMethods(['createEntityManager'])
            ->getMock();

        $package->expects($this->once())->method('createEntityManager')
            ->willReturn(new testEm())
        ;

        $package->onPackagesInit(new WorkflowEvent($app));
    }
}

class testEm
{
    public function getConnection()
    {
        return new testConnection();
    }
}

class testConnection
{
    public function getWrappedConnection()
    {

    }
}
