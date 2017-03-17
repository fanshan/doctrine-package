<?php

    namespace ObjectivePHP\Package\Doctrine;

    use Doctrine\DBAL\Types\Type;
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
            //$application->getConfig()->merge(['doctrine.em.default.entities.locations' => []]);
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
                if(isset($params['db']))
                {
                    $params = $params['db'];
                }


                // normalize if needed
                $entitiesPaths = $params['entities.locations'];

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
                $em       = EntityManager::create($params, $emConfig);

                if(!empty($params['mapping_types']) && is_array($params['mapping_types']))
                {
                    $platform = $em->getConnection()->getDatabasePlatform();
                    foreach($params['mapping_types'] as $type => $mapping)
                    {
                        if (!Type::hasType($type) && class_exists($mapping)) {
                            Type::addType($type, $mapping);
                            $mapping = $type;
                        }
                        $platform->registerDoctrineTypeMapping($type, $mapping);
                    }
                }

                // register entity manager as a service
                $emServiceId = 'doctrine.em.' . Str::cast($connection)->lower();

                $app->getServicesFactory()->registerService(['id' => $emServiceId, 'instance' => $em]);
                $app->getServicesFactory()
                    ->registerService(['id' => 'db.connection.' . $connection, 'instance' => $em->getConnection()
                                                                                                ->getWrappedConnection()])
                ;

            }

        }
    }
