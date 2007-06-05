<?php
error_reporting(E_ALL|E_STRICT);
date_default_timezone_set('Europe/Berlin');

require_once 'Zend/Loader.php';
function __autoload($class)
{
    Zend_Loader::loadClass($class);
}

function p($src, $max_depth = 5) {
    ini_set('xdebug.var_display_max_depth', $max_depth);
    if(function_exists('xdebug_var_dump')) {
        xdebug_var_dump($src);
    } else {
        echo "<pre>";
        print_r($src);
        echo "</pre>";
    }
}

function exceptionsHandler($code, $string, $file, $line) { 
    $exception = new Vps_CustomException($string, $code);
    $exception->setLine($line);
    $exception->setFile($file);
    throw $exception;
} 
set_error_handler('exceptionsHandler', E_ALL);

class Vps_Controller_Front extends Zend_Controller_Front
{
    public static function getInstance()
    {
        $dao = new Vps_Dao(new Zend_Config_Ini('../application/config.db.ini', 'database'));
        Zend_Registry::set('dao', $dao);
        $front = parent::getInstance();

        $front->setDispatcher(new Vps_Controller_Dispatcher());
        $front->setResponse('Vps_Controller_Response_Ajax');
        $router = $front->getRouter();

        $router->AddRoute('default', new Zend_Controller_Router_Route('*', array('controller' => 'web', 'action' => 'index')));
        $router->AddRoute('ajax', new Zend_Controller_Router_Route('ajax/*', array('controller' => 'web', 'action' => 'ajax')));
        $router->AddRoute('ajaxfe', new Zend_Controller_Router_Route('ajax/fe/:action', array('controller' => 'fe', 'action' => 'action')));
        $router->AddRoute('files', new Zend_Controller_Router_Route('files/*', array('controller' => 'web', 'action' => 'files')));
        $router->AddRoute('user', new Zend_Controller_Router_Route('user/:action', array('controller' => 'user', 'action' => 'action')));
        $router->AddRoute('admin', new Zend_Controller_Router_Route('admin/:controller/:action', array('module' => 'admin', 'controller' => 'controller', 'action' => 'action')));
        
        $front->registerPlugin(new Vps_Controller_Plugin_Admin());
        $front->setControllerDirectory('../application/controllers');
        $front->returnResponse(true);

        return $front;
    }
}