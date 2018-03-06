<?php
use Symfony\Component\HttpKernel\Kernel;
use Doctrine\Common\Annotations\AnnotationRegistry;

abstract class Kwf_SymfonyKernel extends Kernel
{
    private static $_instance;

    public function __construct()
    {
        $env = Kwf_Config::getValue('symfony.environment');
        if (in_array($env, array('test', 'dev'))) {
            $environment = $env;
            $debug = true;
            //Debug::enable();
        } else {
            $environment = 'prod';
            $debug = false;
        }
        parent::__construct($environment, $debug);

        AnnotationRegistry::registerLoader(array('Kwf_Loader', 'loadClass'));
    }

    /**
     * Don't use this method in Symfony context
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            $cls = Kwf_Config::getValue('symfony.kernelClass');
            self::$_instance = new $cls();
            self::$_instance->boot(); //make sure it is booted (won't do it twice)
        }
        return self::$_instance;
    }
}
