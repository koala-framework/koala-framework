<?php
Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
Zend_Controller_Action_HelperBroker::addHelper(new Vps_Controller_Action_Helper_ViewRenderer());

class Vps_Controller_Front extends Zend_Controller_Front
{
    protected function _init()
    {
        $this->setControllerDirectory('application/controllers');
        $this->returnResponse(true);
        $this->setParam('disableOutputBuffering', true);

        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Welcome',
                                        'vps_controller_action_welcome');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/User',
                                        'vps_controller_action_user');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Error',
                                        'vps_controller_action_error');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Pool',
                                'vps_controller_action_pool');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Trl',
                                'vps_controller_action_trl');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Debug',
                                'vps_controller_action_debug');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Cli',
                                'vps_controller_action_cli');

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

    public function getRouter()
    {
        if (null == $this->_router) {
            if (isset($_SERVER['SHELL'])) {
                $this->setRouter(new Vps_Controller_Router());
            } else {
                $this->setRouter(new Vps_Controller_Router_Http());
            }
        }

        return $this->_router;
    }

    //funktioniert über __destrukt nicht, workaround:
    public function dispatch(Zend_Controller_Request_Abstract $request = null, Zend_Controller_Response_Abstract $response = null)
    {
        if ($request === null) {
            if (isset($_SERVER['SHELL'])) {
                $request = new Vps_Controller_Request_Cli();
            }
        }
        $ret = parent::dispatch($request, $response);

        $profiler = Zend_Registry::get('db')->getProfiler();
        if ($profiler instanceof Vps_Db_Profiler) {
            $profiler->logSummary();
        }
        return $ret;
    }
}

