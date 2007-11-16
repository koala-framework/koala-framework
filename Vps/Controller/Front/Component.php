<?php
class Vps_Controller_Front_Component extends Vps_Controller_Front
{
    protected function _init()
    {
        parent::_init();

        $router = $this->getRouter();

        $this->setDispatcher(new Vps_Controller_Dispatcher());

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
                    array('componentId'=>null,
                        'module' => 'component',
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
                    'media/:uploadId/:class/:componentId/:checksum/:filename',
                    array('module' => 'vps',
                        'controller' => 'Media',
                        'action' => 'password')));
        $router->AddRoute('mediaoriginal', new Zend_Controller_Router_Route(
                    'media/:uploadId',
                    array('module' => 'vps',
                        'controller' => 'Media',
                        'action' => 'original')));
/*
        $router->AddRoute('mediavpc', new Zend_Controller_Router_Route(
                    'media/:class/:componentId/:filename',
                    array('module' => 'component',
                        'controller' => 'Media',
                        'action' => 'vpc')));*/

        $plugin = new Zend_Controller_Plugin_ErrorHandler();
        $this->registerPlugin($plugin);
    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
            self::$_instance->_init();
        }

        return self::$_instance;
    }
}