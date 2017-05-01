<?php

    namespace ObjectivePHP\Package\Doctrine;

    use Doctrine\DBAL\Types\Type;
    use Doctrine\ORM\EntityManager;
    use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
    use Doctrine\ORM\Tools\Setup;
    use ObjectivePHP\Application\ApplicationInterface;
    use ObjectivePHP\Cli\Router\CliRouter;
    use ObjectivePHP\Package\Doctrine\Command\Doctrine;
    use ObjectivePHP\Primitives\Collection\Collection;
    use ObjectivePHP\Primitives\String\Str;

    /**
     * Class DoctrinePackage
     * @package ObjectivePHP\Package\Doctrine
     */
    class DoctrinePackage
    {

        protected $cliRouterService;


        /**
         * DoctrinePackage constructor.
         * @param string $cliRouterService
         */
        public function __construct($cliRouterService = 'cli.router')
        {
            $this->cliRouterService = $cliRouterService;
        }


        /**
         * @param ApplicationInterface $application
         */
        public function __invoke(ApplicationInterface $application)
        {
            $application->getStep('bootstrap')->plug([$this, 'buildEntityManagers']);

            // register CLI command
            /** @var CliRouter $router */
            $router = $application->getServicesFactory()->get($this->cliRouterService);

            if($router) {
                $router->registerCommand(new Doctrine());
            } else {
                throw new Exception('Cannot find ' . CliRouter::class . ' in ServicesFactory as "' . $this->cliRouterService . '"');
            }

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

                $useSimpleAnnotationReader = true;
                if(isset($params['use_simple_annotation_reader']))
                {
                    $useSimpleAnnotationReader = (bool) $params['use_simple_annotation_reader'];
                }

                // TODO: handle isDev depending on app config
                $emConfig = Setup::createAnnotationMetadataConfiguration((array) $entitiesPaths, true, null ,null, $useSimpleAnnotationReader);
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
