<?php
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
        
class Vps_Controller_Front extends Zend_Controller_Front
{
    public static function setUp()
    {
        error_reporting(E_ALL|E_STRICT);
        date_default_timezone_set('Europe/Berlin');
        set_error_handler('exceptionsHandler', E_ALL);
        
        if (preg_match('#/www/usr/([0-9a-z]+)/#', $_SERVER['SCRIPT_FILENAME'], $m)) {
            $user = $m[1];
        } else if (substr(__FILE__, strlen('/www/public/')) == '/www/public/') {
            $user = 'vivid';
        } else {
            $user = 'production';
        }
        $config = new Zend_Config_Ini('application/config.ini', $user);
        Zend_Registry::set('config', $config);
    }
    
    public static function getInstance($isComponentsWeb = true)
    {
        self::setUp();
        $front = parent::getInstance();

        Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
        Zend_Controller_Action_HelperBroker::addHelper(new Vps_Controller_Action_Helper_ViewRenderer());
             
        $front->setDispatcher(new Vps_Controller_Dispatcher());
        $front->setControllerDirectory('application/controllers');
        $front->returnResponse(true);

        $router = $front->getRouter();
        if ($isComponentsWeb) {
            $router->AddRoute('default', new Zend_Controller_Router_Route('*', array('controller' => 'web', 'action' => 'index')));
            $router->AddRoute('ajax', new Zend_Controller_Router_Route('ajax/*', array('controller' => 'web', 'action' => 'ajax')));
            $router->AddRoute('ajaxfe', new Zend_Controller_Router_Route('ajax/fe/:action', array('controller' => 'fe', 'action' => 'action')));
            $router->AddRoute('user', new Zend_Controller_Router_Route('user/:action', array('module' => 'admin', 'controller' => 'user', 'action' => 'action')));
            $router->AddRoute('component', new Zend_Controller_Router_Route('component/:id/:action', array('module' => 'admin', 'controller' => 'component', 'action' => 'action')));
            
            $front->registerPlugin(new Vps_Controller_Plugin_Admin());

            $dao = new Vps_Dao(new Zend_Config_Ini('application/config.db.ini', 'database'));
            Zend_Registry::set('dao', $dao);
            Zend_Registry::set('db', $dao->getDb());
            Zend_Db_Table_Abstract::setDefaultAdapter($dao->getDb());
        }

        $router->AddRoute('admin', new Zend_Controller_Router_Route('admin/:controller/:action', array('module' => 'admin', 'controller' => 'controller', 'action' => 'action')));

        return $front;
    }
}