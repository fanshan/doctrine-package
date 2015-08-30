<?php

    namespace Tests\ObjectivePHP\DoctrinePackage;
    
    
    use ObjectivePHP\DoctrinePackage\DoctrinePackage;

    class DoctrinePackageTest extends \PHPUnit_Framework_TestCase
    {

        public function testPackageIsCallable()
        {
            $package = new DoctrinePackage();

            $this->assertTrue(is_callable($package));
        }

    }