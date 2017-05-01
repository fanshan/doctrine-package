<?php
/**
 * Created by PhpStorm.
 * User: gde
 * Date: 01/05/2017
 * Time: 17:00
 */

namespace ObjectivePHP\Package\Doctrine\Command;


use Doctrine\ORM\Tools\Console\ConsoleRunner;
use League\CLImate\CLImate;
use ObjectivePHP\Application\ApplicationInterface;
use ObjectivePHP\Cli\Action\AbstractCliAction;
use ObjectivePHP\Cli\Action\Parameter\Param;
use ObjectivePHP\Matcher\Matcher;
use ObjectivePHP\Package\Doctrine\Config\EntityManager;
use ObjectivePHP\ServicesFactory\Specs\ServiceSpecsInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArgvInput;

class Doctrine extends AbstractCliAction
{

    public function __construct()
    {
        $this->setCommand('doctrine');
        $this->setDescription('Doctrine console tool wrapper');
        $this->expects((new Param(['e' => 'entity-manager'], 'Entity manager to use (defaults to "default")')));
        $this->allowUnexpectedParameters();
    }


    /**
     * @param ApplicationInterface $app
     * @return mixed
     */
    public function run(ApplicationInterface $app)
    {

        $entityManagerService = 'doctrine.em.' . $this->getParam('em', 'default');
        $services = $app->getServicesFactory()->getServices();

        if($services->has($entityManagerService)) {

            $helperSet = ConsoleRunner::createHelperSet($app->getServicesFactory()->get($entityManagerService));


            $app = ConsoleRunner::createApplication($helperSet);
            $argv = $GLOBALS['argv'];
            array_shift($argv);
            $input = new ArgvInput($argv);

            $app->run($input);
        }
        else {
            $c = new CLImate();
            $c->red('Unknown entity manager "' . $entityManagerService . '"');
            exit;
        }

    }


}