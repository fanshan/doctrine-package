<?php
    namespace ObjectivePHP\DoctrinePackage\Processor;
    
    
    use Doctrine\ORM\EntityManager;
    use ObjectivePHP\Application\ApplicationAwareInterface;
    use ObjectivePHP\Application\ApplicationInterface;
    use ObjectivePHP\DataProcessor\AbstractDataProcessor;
    use ObjectivePHP\DoctrinePackage\Exception;

    /**
     * Class EntityProcessor
     *
     * @package ObjectivePHP\DoctrinePackage\Parameter
     */
    class EntityProcessor extends AbstractDataProcessor implements ApplicationAwareInterface
    {

        const ENTITY_NOT_FOUND = 'doctrine-package.entity.not-found';

        /**
         * @var string Entity Manager name
         */
        protected $emId = 'default';

        /**
         * @var string
         */
        protected $entity;

        /**
         * @var string
         */
        protected $filter;


        /**
         * @var ApplicationInterface
         */
        protected $application;

        /**
         * Constructor
         *
         * @param string     $reference Parameter reference
         * @param int|string $mapping   Query parameter name or position. If none provided, $reference is used as
         *                              mapping.
         */
        public function __construct()
        {
            parent::__construct();

            // set default messages
            $this->setMessage(self::ENTITY_NOT_FOUND, 'No ":entity" entity was found with ":value" as ":filter"');
        }


        /**
         * @param $value
         */
        public function process($value)
        {

            $emServiceId = 'doctrine.em.' . $this->getEmId();

            $em = $this->getApplication()->getServicesFactory()->get($emServiceId);

            if(!$em)
            {
                throw new Exception(sprintf('Missing entity manager. No service has been registered with name "%s"', $emServiceId));
            }

            if(!$em instanceof EntityManager)
            {
                throw new Exception(sprintf('Invalid entity manager. "%" is not an instance of "Doctrine\ORM\EntityManager"', get_class($em)));
            }

            // get repository
            $repository = $em->getRepository($this->getEntity());

            // define filter
            if($filter = $this->getFilter())
            {
                $entity = $repository->findOneBy([$filter => $value]);
            }
            else
            {
                $entity = $repository->find($value);
            }

            if ($value && !$entity)
            {
                    throw new Exception((string) $this->getMessage(self::ENTITY_NOT_FOUND)
                        ->setVariable('value', $value)
                        ->setVariable('entity', $this->getEntity())
                        ->setVariable('filter', $this->getFilter() ?: 'primary key')
                    );
            }


            return $entity;
        }

        /**
         * @return string
         */
        public function getEmId()
        {
            return $this->emId;
        }

        /**
         * @param string $emId
         *
         * @return $this
         */
        public function setEmId($emId)
        {
            $this->emId = $emId;

            return $this;
        }

        /**
         * @return string
         */
        public function getEntity()
        {
            return $this->entity;
        }

        /**
         * @param string $entity
         *
         * @return $this
         */
        public function setEntity($entity)
        {
            $this->entity = $entity;

            return $this;
        }

        /**
         * @return string
         */
        public function getFilter()
        {
            return $this->filter;
        }

        /**
         * @param string $filter
         *
         * @return $this
         */
        public function setFilter($filter)
        {
            $this->filter = $filter;

            return $this;
        }

        /**
         * @return ApplicationInterface
         */
        public function getApplication()
        {
            return $this->application;
        }

        /**
         * @param ApplicationInterface $application
         *
         * @return $this
         */
        public function setApplication(ApplicationInterface $application)
        {
            $this->application = $application;

            return $this;
        }

    }