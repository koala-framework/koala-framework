<?php
require_once 'Vps/Loader.php';
Vps_Loader::registerAutoload();

function p($src, $max_depth = 5) {
    ini_set('xdebug.var_display_max_depth', $max_depth);
    if(function_exists('xdebug_var_dump')) {
        xdebug_var_dump($src);
    } else {
        echo "<pre>";
        var_dump($src);
        echo "</pre>";
    }
}

function d($src, $max_depth = 5)
{
    p($src, $max_depth);
    exit;
}

class Vps_Controller_Front extends Zend_Controller_Front
{
    public static function setUp()
    {
        error_reporting(E_ALL);
        date_default_timezone_set('Europe/Berlin');
        set_error_handler(array('Vps_Debug', 'handleError'), E_ALL);
        $config = Vps_Setup::createConfig();
        Zend_Registry::set('config', $config);
        $ip = get_include_path();
        foreach ($config->includepath as $p) {
            $ip .= PATH_SEPARATOR . $p;
        }
        set_include_path($ip);
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

            $router->AddRoute('default', new Zend_Controller_Router_Route(
                        '*',
                        array('module' => 'component',
                              'controller' => 'web',
                              'action' => 'index')));
            $router->AddRoute('admin', new Zend_Controller_Router_Route(
                        'admin/:module/:controller/:action',
                        array('module'=>'component',
                              'controller' => 'index',
                              'action' => 'index')));
            $router->AddRoute('components', new Zend_Controller_Router_Route(
                        'admin/components/:action',
                        array('module' => 'component',
                              'controller' => 'components',
                              'action' => 'index')));
            $router->AddRoute('componentshow', new Zend_Controller_Router_Route(
                        'admin/component/show/:class/:componentId',
                        array('module' => 'component',
                              'controller' => 'components',
                              'action' => 'show')));
            $router->AddRoute('componentedit', new Zend_Controller_Router_Route(
                        'admin/component/edit/:class/:action',
                        array('module' => 'component',
                              'controller' => 'component',
                              'action' => 'index')));
            $router->AddRoute('componentsetup', new Zend_Controller_Router_Route(
                        'admin/component/setup/:class',
                        array('module' => 'component',
                              'controller' => 'components',
                              'action' => 'setup')));
            $router->AddRoute('login', new Zend_Controller_Router_Route(
                        'login/:action',
                        array('module' => 'vps',
                              'controller' => 'login',
                              'action' => 'index')));
            $router->AddRoute('menu', new Zend_Controller_Router_Route(
                        'menu/:action',
                        array('module' => 'vps',
                              'controller' => 'menu',
                              'action' => 'index')));

            $router->AddRoute('media', new Zend_Controller_Router_Route(
                        'media/:uploadId/:componentId/:checksum/:filename',
                        array('module' => 'vps',
                              'controller' => 'Media',
                              'action' => 'password')));
            $router->AddRoute('mediaoriginal', new Zend_Controller_Router_Route(
                        'media/:uploadId',
                        array('module' => 'vps',
                              'controller' => 'Media',
                              'action' => 'original')));
            $router->AddRoute('mediavpc', new Zend_Controller_Router_Route(
                        'media/:class/:componentId/:filename',
                        array('module' => 'component',
                              'controller' => 'Media',
                              'action' => 'vpc')));

            $plugin = new Zend_Controller_Plugin_ErrorHandler();
            $plugin->setErrorHandlerModule('component');
            $front->registerPlugin($plugin);

            self::setUpDb();

            // ACL
            $acl = new Vps_Acl();

            // Roles
            $acl->addRole(new Vps_Acl_Role('admin', 'Admin'));

            // Resources
            $acl->add(new Zend_Acl_Resource('web'));
            $acl->add(new Zend_Acl_Resource('media'));
            $acl->add(new Zend_Acl_Resource('mediaoriginal'));
            $acl->add(new Zend_Acl_Resource('mediavpc'));
            //$acl->add(new Zend_Acl_Resource('fe'));
            $acl->add(new Vps_Acl_Resource_MenuUrl('pages',
                array('text'=>'Sitetree', 'icon'=>'application_side_tree.png'),
                '/admin/component/pages/'));
                $acl->add(new Zend_Acl_Resource('pageedit'), 'pages');
                $acl->add(new Zend_Acl_Resource('components'), 'pages'); // für /component/show
                $acl->add(new Zend_Acl_Resource('component'), 'pages'); // für /component/edit

            // Berechtigungen
            $acl->allow(null, 'web');
            $acl->allow(null, 'media');

            $acl->allow('admin', 'pages');
            $acl->allow('admin', 'mediaoriginal');
            $acl->allow('admin', 'mediavpc');

            Zend_Registry::set('acl', $acl);
        }

        return $front;
    }
}