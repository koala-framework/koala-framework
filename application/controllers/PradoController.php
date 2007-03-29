<?php
class PradoController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $basePath = dirname(__FILE__);
        $frameworkPath = 'prado/prado.php';
        $assetsPath = '../public/assets';
        $runtimePath = '../application/prado/runtime';
        $applicationPath = '../application/prado';
        
        if(!is_writable($assetsPath))
            die("Please make sure that the directory $assetsPath is writable by Web server process.");
        if(!is_writable($runtimePath))
            die("Please make sure that the directory $runtimePath is writable by Web server process.");
        
        require_once($frameworkPath);
        
        $application = new TApplication($applicationPath);
        $application->run();
    }
}
?>