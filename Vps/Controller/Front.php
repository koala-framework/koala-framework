<?php
Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
Zend_Controller_Action_HelperBroker::addHelper(new Vps_Controller_Action_Helper_ViewRenderer());

class Vps_Controller_Front extends Zend_Controller_Front
{
    protected function _init()
    {
        $this->setControllerDirectory('application/controllers');
        $this->returnResponse(true);

        $this->setDispatcher(new Vps_Controller_Dispatcher());
        $router = $this->getRouter();
        $router->AddRoute('vps', new Zend_Controller_Router_Route(
                    '/vps/:controller/:action',
                    array('module' => 'vps',
                          'action'=>'index')));

        $plugin = new Zend_Controller_Plugin_ErrorHandler();
        $plugin->setErrorHandlerModule('vps');
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
