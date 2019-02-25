<?php
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Doctrine\Common\Annotations\AnnotationRegistry;

abstract class Kwf_SymfonyKernel extends Kernel
{
    public function __construct()
    {
        $environment = (Kwf_Config::getValue('symfony.environment.name')) ? Kwf_Config::getValue('symfony.environment.name') : 'prod';
        $debug = (Kwf_Config::getValue('symfony.environment.debug')) ? Kwf_Config::getValue('symfony.environment.debug') : false;

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
        $parametersLocal = $this->getRootDir() . '/config/parameters.local.yml';
        if (file_exists($parametersLocal) && is_readable($parametersLocal)) {
            $loader->load($parametersLocal); // local parameters always win
        }
    }
}
