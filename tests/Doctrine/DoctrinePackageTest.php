<?php

    namespace Tests\ObjectivePHP\Package\Doctrine;
    
    
    use ObjectivePHP\Package\Doctrine\DoctrinePackage;

    class DoctrinePackageTest extends \PHPUnit_Framework_TestCase
    {

        public function testPackageIsCallable()
        {
            $package = new DoctrinePackage();

            $this->assertTrue(is_callable($package));
        }

    }