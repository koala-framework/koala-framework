<?php
Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
Zend_Controller_Action_HelperBroker::addHelper(new Vps_Controller_Action_Helper_ViewRenderer());

class Vps_Controller_Front extends Zend_Controller_Front
{
    protected function _init()
    {
        $this->setControllerDirectory('application/controllers');
        $this->returnResponse(true);

        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Welcome',
                                        'vps_controller_action_welcome');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/User',
                                        'vps_controller_action_user');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Error',
                                        'vps_controller_action_error');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Media',
                                        'vps_controller_action_media');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Pool',
                                'vps_controller_action_pool');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Trl',
                                'vps_controller_action_trl');

        $router = $this->getRouter();

        $router->AddRoute('vps_welcome', new Zend_Controller_Router_Route(
                    '/vps/welcome/:controller/:action',
                    array('module'     => 'vps_controller_action_welcome',
                          'controller' =>'index',
                          'action'     =>'index')));
        $router->AddRoute('vps_user', new Zend_Controller_Router_Route(
                    '/vps/user/:controller/:action',
                    array('module'     => 'vps_controller_action_user',
                          'action'     =>'index')));
        $router->AddRoute('vps_error', new Zend_Controller_Router_Route(
                    '/vps/error/:controller/:action',
                    array('module'     => 'vps_controller_action_error')));
        $router->AddRoute('vps_start', new Zend_Controller_Router_Route(
                    '/vps/start',
                    array('module'      => 'vps_controller_action_welcome',
                          'controller'  => 'start',
                          'action'      => 'index')));
        $router->AddRoute('vps_pool', new Zend_Controller_Router_Route(
                    '/vps/pool/:controller/:action',
                    array('module'     => 'vps_controller_action_pool',
                          'controller' => 'pools',
                          'action'     => 'index')));
        $router->AddRoute('media', new Zend_Controller_Router_Route(
                    '/media/:table/:id/:rule/:type/:checksum/:filename',
                    array('module'     => 'vps_controller_action_media',
                          'controller' => 'media',
                          'action'     => 'password')));
        $router->AddRoute('trl', new Zend_Controller_Router_Route(
                    '/vps/trl/:controller/:action',
                    array('module'     => 'vps_controller_action_trl',
                          'controller' => 'index',
                          'action'     => 'index')));

        $plugin = new Zend_Controller_Plugin_ErrorHandler();
        $plugin->setErrorHandlerModule('vps_controller_action_error');
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

    //funktioniert über __destrukt nicht, workaround:
    public function dispatch()
    {
        $ret = parent::dispatch();

        $profiler = Zend_Registry::get('db')->getProfiler();
        if ($profiler instanceof Vps_Db_Profiler) {
            $profiler->logSummary();
        }
        return $ret;
    }
}

