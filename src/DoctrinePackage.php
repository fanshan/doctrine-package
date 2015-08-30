<?php

    namespace ObjectivePHP\DoctrinePackage;

    use ObjectivePHP\Application\Workflow\Event\WorkflowEvent;

    class DoctrinePackage
    {
        public function __invoke(WorkflowEvent $event)
        {
            // hook package from here
        }
    }