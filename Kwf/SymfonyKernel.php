<?php
use Symfony\Component\HttpKernel\Kernel;
use Doctrine\Common\Annotations\AnnotationRegistry;

abstract class Kwf_SymfonyKernel extends Kernel
{
    public function __construct()
    {
        if (Kwf_Exception::isDebug()) {
            $environment = 'dev';
            $debug = true;
            //Debug::enable();
        } else {
            $environment = 'prod';
            $debug = false;
        }
        parent::__construct($environment, $debug);

        AnnotationRegistry::registerLoader(array('Kwf_Loader', 'loadClass'));
    }

    public function locateResource($name, $dir = null, $first = true)
    {
        if (substr($name, 0, 4) == '@Kwc') {
            if (!$first) throw new \Kwf_Exception_NotYetImplemented();
            $componentClass = substr($name, 4, strpos($name, '/')-4);
            $name = substr($name, strpos($name, '/')+1);
            $paths = \Kwc_Abstract::getSetting($componentClass, 'parentFilePaths');
            foreach ($paths as $path=>$cls) {
                if (file_exists($path.'/'.$name)) {
                    return $path.'/'.$name;
                }
            }
            throw new \Kwf_Exception();
        } else {
            return parent::locateResource($name, $dir, $first);
        }
    }


}
