<?php
Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
Zend_Controller_Action_HelperBroker::addHelper(new Vps_Controller_Action_Helper_ViewRenderer());

class Vps_Controller_Front extends Zend_Controller_Front
{
    private $_webRouter;

    protected function _init()
    {
        $this->setDispatcher(new Vps_Controller_Dispatcher());

        $this->setControllerDirectory('application/controllers');
        $this->returnResponse(true);
        $this->setParam('disableOutputBuffering', true);

        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Welcome',
                                        'vps_controller_action_welcome');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/User',
                                        'vps_controller_action_user');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/ProjectTimer',
                                        'vps_controller_action_project-timer');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Error',
                                        'vps_controller_action_error');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Pool',
                                'vps_controller_action_pool');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Todo',
                                'vps_controller_action_todo');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Debug',
                                'vps_controller_action_debug');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Cli',
                                'vps_controller_action_cli');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Cli/Web',
                                'vps_controller_action_cli_web');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Cli/Svn',
                                'vps_controller_action_cli_svn');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Media',
                                'vps_controller_action_media');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Spam',
                                        'vps_controller_action_spam');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Enquiries',
                                        'vps_controller_action_enquiries');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Redirects',
                                        'vps_controller_action_redirects');
        $this->addControllerDirectory(VPS_PATH . '/tests', 'vps_test');
        $this->addControllerDirectory('tests', 'web_test');
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Trl',
                                'vps_controller_action_trl');
        if (file_exists('application/controllers/Cli')) {
            $this->addControllerDirectory('application/controllers/Cli', 'cli');
        }
        $this->addControllerDirectory(VPS_PATH . '/Vps/Controller/Action/Component',
                                        'vps_controller_action_component');


        $plugin = new Zend_Controller_Plugin_ErrorHandler();
        $plugin->setErrorHandlerModule('vps_controller_action_error');
        if (php_sapi_name() == 'cli') {
            $plugin->setErrorHandlerController('cli');
        }
        $this->registerPlugin($plugin);
    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            $class = Vps_Registry::get('config')->frontControllerClass;
            if (!$class) {
                throw new Vps_Exception("frontControllerClass must be set in application/config.ini");
            }
            self::$_instance = new $class();
            self::$_instance->_init();
        }

        return self::$_instance;
    }

    public final function getRouter()
    {
        if (null == $this->_router) {
            if (php_sapi_name() == 'cli') {
                $this->setRouter($this->_getDefaultCliRouter());
            } else {
                $this->setRouter($this->getWebRouter());
            }
        }

        return $this->_router;
    }

    protected function _getDefaultCliRouter()
    {
        return new Vps_Controller_Router_Cli();
    }

    public function getWebRouter()
    {
        if (isset($this->_webRouter)) {
            return $this->_webRouter;
        } else {
            return $this->_getDefaultWebRouter();
        }
    }

    protected function _getDefaultWebRouter()
    {
        return new Vps_Controller_Router('');
    }

    public function setWebRouter(Zend_Controller_Router_Interface $router)
    {
        $this->_webRouter = $router;
    }

    public function dispatch(Zend_Controller_Request_Abstract $request = null, Zend_Controller_Response_Abstract $response = null)
    {
        if ($request === null) {
            if (php_sapi_name() == 'cli') {
                $request = new Vps_Controller_Request_Cli();
            } else {
                $request = new Vps_Controller_Request_Http();
            }
        }
        $ret = parent::dispatch($request, $response);
        Vps_Benchmark::shutDown();
        return $ret;
    }
}

