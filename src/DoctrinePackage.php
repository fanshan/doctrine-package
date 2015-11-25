<?php

    namespace ObjectivePHP\DoctrinePackage;

    use Doctrine\ORM\EntityManager;
    use Doctrine\ORM\Tools\Setup;
    use ObjectivePHP\Application\Workflow\Event\WorkflowEvent;
    use ObjectivePHP\Config\Config;
    use ObjectivePHP\Primitives\Collection\Collection;
    use ObjectivePHP\Primitives\String\Str;

    class DoctrinePackage
    {
        public function __invoke(WorkflowEvent $event)
        {
            $workflow = $event->getApplication()->getWorkflow();
            $workflow->bind('packages.post', [$this, 'buildEntityManagers']);

            // ensure several entities location can be provided
            $event->getApplication()->getConfig()->merge(['doctrine.em.default.entities.locations' => []]);
        }


        public function buildEntityManagers(WorkflowEvent $event)
        {
            $config = $event->getApplication()->getConfig()->doctrine;

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

                $event->getApplication()->getServicesFactory()->registerService(['id' => $emServiceId, 'instance' => $em]);

            }

        }
    }