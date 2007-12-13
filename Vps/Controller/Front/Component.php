<?php
class Vps_Controller_Front_Component extends Vps_Controller_Front
{
    protected function _init()
    {
        $this->setDispatcher(new Vps_Controller_Dispatcher());

        $router = $this->getRouter();

        $router->AddRoute('default', new Zend_Controller_Router_Route(
                    '*',
                    array('module' => 'vps_controller_action_component',
                          'controller' => 'web',
                          'action' => 'index')));
        $router->AddRoute('component', new Zend_Controller_Router_Route(
                    'admin/component/:controller/:action',
                    array('module'=>'vps_controller_action_component',
                        'controller' => 'index',
                        'action' => 'index')));
        $router->AddRoute('components', new Zend_Controller_Router_Route(
                    'admin/components/:action',
                    array('module' => 'vps_controller_action_component',
                        'controller' => 'components',
                        'action' => 'index')));
        $router->AddRoute('componentshow', new Zend_Controller_Router_Route(
                    'admin/component/show/:class/:componentId',
                    array('componentId'=>null,
                        'module' => 'vps_controller_action_component',
                        'controller' => 'components',
                        'action' => 'show')));
        $router->AddRoute('componentedit', new Zend_Controller_Router_Route(
                    'admin/component/edit/:class/:action',
                    array('module' => 'component',
                        'controller' => 'component',
                        'action' => 'index')));
        $router->AddRoute('media', new Zend_Controller_Router_Route(
                    'media/:uploadId/:class/:componentId/:type/:checksum/:filename',
                    array('module' => 'vps_controller_action_component',
                          'controller' => 'Media',
                          'action' => 'password')));
        $router->AddRoute('mediaoriginal', new Zend_Controller_Router_Route(
                    'media/:uploadId',
                    array('module' => 'vps_controller_action_component',
                          'controller' => 'Media',
                          'action' => 'original')));
        $router->AddRoute('admin', new Zend_Controller_Router_Route(
                    'admin/:module/:controller/:action',
                    array('module'=>'',
                        'controller' => 'index',
                        'action' => 'index')));
        parent::_init();
        $this->addControllerDirectory('Vps/Controller/Action/Component',
                                        'vps_controller_action_component');

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
