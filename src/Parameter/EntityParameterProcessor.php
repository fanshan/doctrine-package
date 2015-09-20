<?php
    namespace ObjectivePHP\DoctrinePackage\Parameter;
    
    
    use Doctrine\ORM\EntityManager;
    use ObjectivePHP\Application\Action\Parameter\AbstractParameterProcessor;
    use ObjectivePHP\DoctrinePackage\Exception;

    /**
     * Class EntityParameterProcessor
     *
     * @package ObjectivePHP\DoctrinePackage\Parameter
     */
    class EntityParameterProcessor extends AbstractParameterProcessor
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
         * Constructor
         *
         * @param string     $reference Parameter reference
         * @param int|string $mapping   Query parameter name or position. If none provided, $reference is used as
         *                              mapping.
         */
        public function __construct($reference, $mapping = null)
        {
            parent::__construct($reference, $mapping);

            // set default messages
            $this->setMessage(self::ENTITY_NOT_FOUND, 'No entity was found with given parameter ":param" with value ":value"');
        }


        /**
         * @param $value
         */
        public function process($value)
        {

            if($this->isMandatory())
            {
                if(is_null($value))
                {
                    throw new \ObjectivePHP\Application\Exception($this->getMessage());
                }
            }

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

            if ($this->isMandatory())
            {
                if (empty($entity))
                {
                    throw new \ObjectivePHP\Application\Exception($this->getMessage(self::ENTITY_NOT_FOUND)->setVariable(['value' => $value]));
                }
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

    }