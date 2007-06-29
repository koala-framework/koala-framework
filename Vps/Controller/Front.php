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
        Zend_Registry::set('config', Vps_Setup::createConfig());
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
            $router->AddRoute('subcomponent', new Zend_Controller_Router_Route('component/:id/:controller/:action', array('module' => 'component', 'controller' => 'controller', 'action' => 'action')));
            $router->AddRoute('component', new Zend_Controller_Router_Route('component/:id/:action', array('module' => 'component', 'controller' => 'index', 'action' => 'action')));
            $router->AddRoute('indexcomponent', new Zend_Controller_Router_Route('component/:id', array('module' => 'component', 'controller' => 'index', 'action' => 'index')));
            
            $front->registerPlugin(new Vps_Controller_Plugin_Admin());

            $dao = new Vps_Dao(new Zend_Config_Ini('application/config.db.ini', 'database'));
            Zend_Registry::set('dao', $dao);
            $db = $dao->getDb();
            $db->query('SET names UTF8');
            Zend_Registry::set('db', $db);
            Zend_Db_Table_Abstract::setDefaultAdapter($db);

            // ACL
            $acl = new Vps_Acl();
    
            // Roles
            $acl->addRole(new Vps_Acl_Role('guest'));
            $acl->addRole(new Vps_Acl_Role('admin'));
            
            // Resources
            $acl->add(new Zend_Acl_Resource('web'));
            $acl->add(new Vps_Acl_Resource('admin', 'Admin'));
                $acl->add(new Vps_Acl_Resource('admin_pages', 'Seitenbaum', '/admin/pages'), 'admin');
                $acl->add(new Zend_Acl_Resource('admin_page'), 'admin');
                $acl->add(new Zend_Acl_Resource('admin_component'), 'admin');
                $acl->add(new Zend_Acl_Resource('admin_menu'), 'admin');
            
            // Berechtigungen
            $acl->allow('admin', 'admin');
            $acl->allow('admin', 'web');
            $acl->allow('guest', 'web');
            
            Zend_Registry::set('acl', $acl);
        }

        $router->AddRoute('admin', new Zend_Controller_Router_Route('admin/:controller/:action', array('module' => 'admin', 'controller' => 'controller', 'action' => 'action')));

        return $front;
    }
}