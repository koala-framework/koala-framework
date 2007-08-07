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

function d($src, $max_depth = 5)
{
    p($src, $max_depth);
    exit;
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
        error_reporting(E_ALL);
        date_default_timezone_set('Europe/Berlin');
        set_error_handler('exceptionsHandler', E_ALL);
        Zend_Registry::set('config', Vps_Setup::createConfig());
    }
    
    public static function setUpDb()
    {
        $dao = new Vps_Dao(new Zend_Config_Ini('application/config.db.ini', 'database'));
        Zend_Registry::set('dao', $dao);
        $db = $dao->getDb();
        $db->query('SET names UTF8');
        Zend_Registry::set('db', $db);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
    }
    
    public static function getInstance($isComponentsWeb = true)
    {
        self::setUp();
        $front = parent::getInstance();

        Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
        Zend_Controller_Action_HelperBroker::addHelper(new Vps_Controller_Action_Helper_ViewRenderer());

        $front->setControllerDirectory('application/controllers');
        $front->returnResponse(true);

        $router = $front->getRouter();
        if ($isComponentsWeb) {
            $front->setDispatcher(new Vps_Controller_Dispatcher());

            $router->AddRoute('default', new Zend_Controller_Router_Route('*', array('controller' => 'web', 'action' => 'index')));
            $router->AddRoute('ajax', new Zend_Controller_Router_Route('ajax/*', array('controller' => 'web', 'action' => 'ajax')));
            $router->AddRoute('ajaxfe', new Zend_Controller_Router_Route('ajax/fe/:action', array('controller' => 'fe', 'action' => 'action')));
            $router->AddRoute('componentshow', new Zend_Controller_Router_Route('component/:action/:componentId', array('module' => 'admin', 'controller' => 'components', 'action' => 'show')));
            $router->AddRoute('componentedit', new Zend_Controller_Router_Route('component/edit/:componentId/:action', array('module' => 'component', 'controller' => 'Index', 'action' => 'index')));
            $router->AddRoute('admin', new Zend_Controller_Router_Route('admin/:controller/:action', array('module' => 'admin', 'controller' => 'controller', 'action' => 'index')));
            $router->AddRoute('login', new Zend_Controller_Router_Route('login/:action', array('module' => 'admin', 'controller' => 'login', 'action' => 'index')));
            
            $plugin = new Zend_Controller_Plugin_ErrorHandler();
            $plugin->setErrorHandlerModule('admin');
            $front->registerPlugin($plugin);
            
            self::setUpDb();

            // ACL
            $acl = new Vps_Acl();
    
            // Roles
            $acl->addRole(new Vps_Acl_Role('member'), 'guest');
            $acl->addRole(new Vps_Acl_Role('admin'), 'member');
            
            // Resources
            $acl->add(new Zend_Acl_Resource('web'));
            $acl->add(new Zend_Acl_Resource('fe'));
            $acl->add(new Vps_Acl_Resource_MenuDropdown('admin', 'Admin'));
                $acl->add(new Vps_Acl_Resource_MenuUrl('pages', 'Seitenbaum', '/admin/pages/'), 'admin');
                $acl->add(new Zend_Acl_Resource('pageedit'), 'admin');
                $acl->add(new Vps_Acl_Resource_MenuUrl('components', 'Komponentenübersicht', '/admin/components/'), 'admin');
                    $acl->add(new Zend_Acl_Resource('component'), 'admin');
            
            // Berechtigungen
            $acl->allow('admin', 'web');
            $acl->allow('admin', 'fe');
            $acl->allow('admin', 'admin');
            $acl->allow('member', 'fe');
            $acl->allow('guest', 'web');
            
            Zend_Registry::set('acl', $acl);
        }


        return $front;
    }
}