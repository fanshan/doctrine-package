<?php

    namespace ObjectivePHP\Package\Doctrine;

    use Doctrine\ORM\EntityManager;
    use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
    use Doctrine\ORM\Tools\Setup;
    use ObjectivePHP\Application\ApplicationInterface;
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
            $entityManagers = $app->getConfig()->subset(Config\EntityManager::class);

            foreach ($entityManagers as $connection => $params)
            {

                // normalize if needed
                $entitiesPaths = $params['db']['entities.locations'];

                Collection::cast($entitiesPaths)->each(function (&$path)
                {
                    if (strpos($path, '/') !== 0)
                    {
                        $path = getcwd() . '/' . $path;
                    }
                });

                // TODO: handle isDev depending on app config
                $emConfig = Setup::createAnnotationMetadataConfiguration((array) $entitiesPaths, true);
                $emConfig->setNamingStrategy(new UnderscoreNamingStrategy());
                $em       = EntityManager::create($params['db'], $emConfig);

                if(!empty($params['db']['mapping_types']) && is_array($params['db']['mapping_types']))
                {
                    $platform = $em->getConnection()->getDatabasePlatform();
                    foreach($params['db']['mapping_types'] as $type => $mapping)
                    {
                        $platform->registerDoctrineTypeMapping($type, $mapping);
                    }
                }


                // register entity manager as a service
                $emServiceId = 'doctrine.em.' . Str::cast($connection)->lower();

                $app->getServicesFactory()->registerService(['id' => $emServiceId, 'instance' => $em]);

            }

        }
    }
