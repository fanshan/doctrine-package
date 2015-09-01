<?php
    /**
     * Created by PhpStorm.
     * User: gauthier
     * Date: 01/09/15
     * Time: 18:07
     */
    
    namespace ObjectivePHP\DoctrinePackage\Parameter;
    
    
    use Doctrine\ORM\EntityManager;
    use ObjectivePHP\Application\Action\Param\AbstractExpectation;
    use ObjectivePHP\DoctrinePackage\Exception;

    class EntityParameter extends AbstractExpectation
    {
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