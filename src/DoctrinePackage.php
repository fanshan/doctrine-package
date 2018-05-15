<?php

namespace ObjectivePHP\Package\Doctrine;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\Tools\Setup;
use ObjectivePHP\Application\Package\PackageInterface;
use ObjectivePHP\Application\Workflow\PackagesInitListener;
use ObjectivePHP\Application\Workflow\WorkflowEventInterface;
use ObjectivePHP\Package\Doctrine\Config\EntityManager as EM;
use ObjectivePHP\Config\ConfigAccessorsTrait;
use ObjectivePHP\Config\ConfigInterface;
use ObjectivePHP\Config\ConfigProviderInterface;
use ObjectivePHP\Primitives\String\Str;
use ObjectivePHP\Config\Config;
use ObjectivePHP\ServicesFactory\ServicesFactory;

/**
 * Class DoctrinePackage
 * @package Fei\Service\SecondPartyLogistics\Tool\Package\Doctrine
 */
class DoctrinePackage implements PackageInterface, ConfigProviderInterface, PackagesInitListener
{
    use ConfigAccessorsTrait;

    const SERVICE_PREFIX = 'doctrine.em.';

    /**
     * {@inheritdoc}
     */
    public function getConfig(): ConfigInterface
    {
        return new Config(new EM());
    }

    /**
     * {@inheritdoc}
     */
    public function onPackagesInit(WorkflowEventInterface $event)
    {
        $this->registerServices(
            $event->getApplication()->getServicesFactory(),
            $event->getApplication()->getConfig()->get(EM::KEY)
        );
    }

    /**
     * Register Connection and EntityManager Services
     *
     * @param ServicesFactory $servicesFactory
     * @param EM[]            $entityManagers
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \ObjectivePHP\ServicesFactory\Exception\ServicesFactoryException
     */
    public function registerServices(ServicesFactory $servicesFactory, array $entityManagers)
    {
        foreach ($entityManagers as $name => $entityManager) {
            //TODO: handle isDev depending on app config
            $emConfig = Setup::createAnnotationMetadataConfiguration(
                (array) $entityManager->getEntities(),
                true,
                null,
                null,
                true
            );

            $emConfig->setNamingStrategy(new UnderscoreNamingStrategy());

            $em = $this->createEntityManager($entityManager->toArray(), $emConfig);

            // register entity manager as a service
            $emServiceId = 'doctrine.em.' . Str::cast($name)->lower();

            $servicesFactory->registerService(['id' => $emServiceId, 'instance' => $em]);
            $servicesFactory->registerService([
                'id' => 'db.connection.' . $name,
                'instance' => $em->getConnection()->getWrappedConnection()
            ]);
        }
    }

    /**
     * Factory for create a Doctrine EntityManager instance
     *
     * @param mixed         $conn   An array with the connection parameters or an existing Connection instance.
     * @param Configuration $config
     *
     * @return EntityManager
     * @throws \Doctrine\ORM\ORMException
     */
    protected function createEntityManager($conn, Configuration $config)
    {
        return EntityManager::create($conn, $config);
    }
}
