<?php

namespace ObjectivePHP\Package\Doctrine;

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

/**
 * Class DoctrinePackage
 * @package Fei\Service\SecondPartyLogistics\Tool\Package\Doctrine
 */
class DoctrinePackage implements PackageInterface, ConfigProviderInterface, PackagesInitListener
{
    use ConfigAccessorsTrait;

    const SERVICE_PREFIX = 'doctrine.em.';

    /**
     * @return ConfigInterface
     */
    public function getConfig(): ConfigInterface
    {
        return new Config(new EM());
    }

    /**
     * @param WorkflowEventInterface $event
     */
    public function onPackagesInit(WorkflowEventInterface $event)
    {
        $params = $event->getApplication()->getConfig()->get(EM::KEY);

        foreach ($params as $name => $parameters) {
            $parameters = $parameters->toArray();

            // normalize if needed
            $entitiesPaths = $parameters['entities'];

            // TODO: handle isDev depending on app config
            $emConfig = Setup::createAnnotationMetadataConfiguration(
                (array) $entitiesPaths,
                true,
                null,
                null,
                true
            );
            $emConfig->setNamingStrategy(new UnderscoreNamingStrategy());

            $em = $this->createEm($parameters, $emConfig);


            // register entity manager as a service
            $emServiceId = 'doctrine.em.' . Str::cast($name)->lower();

            $event->getApplication()->getServicesFactory()->registerService(['id' => $emServiceId, 'instance' => $em]);
            $event->getApplication()->getServicesFactory()
                ->registerService(['id' => 'db.connection.' . $name, 'instance' => $em->getConnection()
                                                                                            ->getWrappedConnection()])
                ;
        }
    }

    /**
     * @param $params
     * @param $config
     * @return EntityManager
     * @codeCoverageIgnore
     */
    protected function createEm($params, $config)
    {
        return EntityManager::create($params, $config);
    }
}
