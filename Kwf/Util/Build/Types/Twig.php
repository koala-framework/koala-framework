<?php
class Kwf_Util_Build_Types_Twig extends Kwf_Util_Build_Types_Abstract
{
    protected function _build($options)
    {
        if (file_exists('build/twig')) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator('build/twig', RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $fileinfo) {
                $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                $todo($fileinfo->getRealPath());
            }
        }

        $env = new Kwf_Component_Renderer_Twig_Environment(null);
        $env->setCompilerEnabled(true);
        foreach (Kwf_Component_Settings::getComponentClasses() as $class) {
            $files = Kwf_Component_Abstract_Admin::getComponentFiles($class, array(
                'Master' => array('filename'=>'Master', 'ext'=>array('twig'), 'returnClass'=>false),
                'Component' => array('filename'=>'Component', 'ext'=>array('twig'), 'returnClass'=>false),
                'Partial' => array('filename'=>'Partial', 'ext'=>array('twig'), 'returnClass'=>false),
                'Mail.html' => array('filename'=>'Mail.html', 'ext'=>array('twig'), 'returnClass'=>false),
                'Mail.txt' => array('filename'=>'Mail.txt', 'ext'=>array('twig'), 'returnClass'=>false),
            ));
            foreach ($files as $i) {
                if ($i) {
                    $cacheFileName = $env->getCacheFilename($i);
                    if (!file_exists($cacheFileName)) {
                        if (!file_exists(dirname($cacheFileName))) mkdir(dirname($cacheFileName), 0777, true);
                        file_put_contents($cacheFileName, $env->compileSource(file_get_contents($i), $i));
                    }
                }
            }
        }
    }

    public function getTypeName()
    {
        return 'twig';
    }
}
