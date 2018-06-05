<?php
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Doctrine\Common\Annotations\AnnotationRegistry;

abstract class Kwf_SymfonyKernel extends Kernel
{
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

    public function getRootDir()
    {
        return getcwd() . "/symfony";
    }

    public function getCacheDir()
    {
        return getcwd() . "/cache/symfony/{$this->getEnvironment()}";
    }

    public function getLogDir()
    {
        return getcwd() . "/log/symfony";
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load("{$this->getRootDir()}/config/config_{$this->getEnvironment()}.yml");
    }
}
