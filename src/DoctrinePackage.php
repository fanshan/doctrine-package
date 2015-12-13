<?php

    namespace ObjectivePHP\DoctrinePackage;

    use Doctrine\ORM\EntityManager;
    use Doctrine\ORM\Tools\Setup;
    use ObjectivePHP\Application\ApplicationInterface;
    use ObjectivePHP\Application\Workflow\Event\WorkflowEvent;
    use ObjectivePHP\Config\Config;
    use ObjectivePHP\Primitives\Collection\Collection;
    use ObjectivePHP\Primitives\String\Str;

    class DoctrinePackage
    {
        public function __invoke(ApplicationInterface $application)
        {
            $application->getStep('bootstrap')->plug([$this, 'buildEntityManagers']);

            // initialize entities locations
            $application->getConfig()->merge(['doctrine.em.default.entities.locations' => []]);
        }


        /**
         * @param ApplicationInterface $app
         *
         * @throws \Doctrine\ORM\ORMException
         * @throws \ObjectivePHP\Primitives\Exception
         * @throws \ObjectivePHP\ServicesFactory\Exception
         */
        public function buildEntityManagers(ApplicationInterface $app)
        {
            $config = $app->getConfig()->doctrine;

            foreach($config->em->toArray() as $connection => $params)
            {
                $params = Config::cast($params);
                $dbParams = Config::cast($params->db);

                // normalize if needed
                if($dbParams->has('name')) $dbParams->rename('name', 'dbname');

                $entitiesPaths = $params->entities['locations'];

                $entitiesPaths = Collection::cast($entitiesPaths);

                $entitiesPaths->each(function (&$path)
                {
                    if(strpos($path, '/') !== 0)
                    {
                        $path = getcwd() . '/' . $path;
                    }
                });

                // TODO: handle isDev depending on app config
                $emConfig = Setup::createAnnotationMetadataConfiguration($entitiesPaths->toArray(), true);
                $em = EntityManager::create($dbParams->toArray(), $emConfig);

                // register entity manager as a service
                $emServiceId = 'doctrine.em.' . Str::cast($connection)->lower();

                $app    ->getServicesFactory()->registerService(['id' => $emServiceId, 'instance' => $em]);

            }

        }
    }