<?php
Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
Zend_Controller_Action_HelperBroker::addHelper(new Kwf_Controller_Action_Helper_ViewRenderer());

class Kwf_Controller_Front extends Zend_Controller_Front
{
    private $_webRouter;

    protected function _init()
    {
        $this->setDispatcher(new Kwf_Controller_Dispatcher());

        $this->setControllerDirectory('controllers');
        $this->returnResponse(true);
        $this->setParam('disableOutputBuffering', true);

        $this->addControllerDirectory(KWF_PATH . '/Kwf/Controller/Action/Welcome',
                                        'kwf_controller_action_welcome');
        $this->addControllerDirectory(KWF_PATH . '/Kwf/Controller/Action/User',
                                        'kwf_controller_action_user');
        $this->addControllerDirectory(KWF_PATH . '/Kwf/Controller/Action/Error',
                                        'kwf_controller_action_error');
        $this->addControllerDirectory(KWF_PATH . '/Kwf/Controller/Action/Pool',
                                'kwf_controller_action_pool');
        $this->addControllerDirectory(KWF_PATH . '/Kwf/Controller/Action/Debug',
                                'kwf_controller_action_debug');
        $this->addControllerDirectory(KWF_PATH . '/Kwf/Controller/Action/Cli',
                                'kwf_controller_action_cli');
        $this->addControllerDirectory(KWF_PATH . '/Kwf/Controller/Action/Cli/Web',
                                'kwf_controller_action_cli_web');
        $this->addControllerDirectory(KWF_PATH . '/Kwf/Controller/Action/Media',
                                'kwf_controller_action_media');
        $this->addControllerDirectory(KWF_PATH . '/Kwf/Controller/Action/Spam',
                                        'kwf_controller_action_spam');
        $this->addControllerDirectory(KWF_PATH . '/Kwf/Controller/Action/Enquiries',
                                        'kwf_controller_action_enquiries');
        $this->addControllerDirectory(KWF_PATH . '/Kwf/Controller/Action/Redirects',
                                        'kwf_controller_action_redirects');
        $this->addControllerDirectory(KWF_PATH . '/Kwf/Controller/Action/Trl',
                                'kwf_controller_action_trl');
        $this->addControllerDirectory(KWF_PATH . '/Kwf/Controller/Action/MaintenanceJobs',
                                'kwf_controller_action_maintenance-jobs');
        if (file_exists('controllers/Cli')) {
            $this->addControllerDirectory('controllers/Cli', 'cli');
        }
        $this->addControllerDirectory(KWF_PATH . '/Kwf/Controller/Action/Component',
                                        'kwf_controller_action_component');

        if (is_dir('controllers')) {
            //automatically add controller directories from web based on existing directories in filesystem in web
            $iterator = new DirectoryIterator('controllers');
            $filter = new Zend_Filter_Word_CamelCaseToDash();
            foreach($iterator as $fileinfo) {
                if (!$fileinfo->isDot() && $fileinfo->isDir() && $fileinfo->getBasename() != 'Cli') {
                    $this->addControllerDirectory($fileinfo->getPathname(), strtolower($filter->filter($fileinfo->getBasename())));
                }
            }
        }


        $plugin = new Zend_Controller_Plugin_ErrorHandler();
        $plugin->setErrorHandlerModule('kwf_controller_action_error');
        if (PHP_SAPI == 'cli') {
            $plugin->setErrorHandlerController('cli');
        }
        $this->registerPlugin($plugin);
    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            $class = Kwf_Config::getValue('frontControllerClass');
            if (!$class) {
                throw new Kwf_Exception("frontControllerClass must be set in config.ini");
            }
            self::$_instance = new $class();
            self::$_instance->_init();
        }

        return self::$_instance;
    }

    public final function getRouter()
    {
        if (null == $this->_router) {
            if (PHP_SAPI == 'cli') {
                $this->setRouter($this->_getDefaultCliRouter());
            } else {
                $this->setRouter($this->getWebRouter());
            }
        }

        return $this->_router;
    }

    protected function _getDefaultCliRouter()
    {
        return new Kwf_Controller_Router_Cli();
    }

    public function getWebRouter()
    {
        if (isset($this->_webRouter)) {
            return $this->_webRouter;
        } else {
            $this->_webRouter = $this->_getDefaultWebRouter();
            return $this->_webRouter;
        }
    }

    protected function _getDefaultWebRouter()
    {
        return new Kwf_Controller_Router('');
    }

    public function setWebRouter(Zend_Controller_Router_Interface $router)
    {
        $this->_webRouter = $router;
    }

    public function dispatch(Zend_Controller_Request_Abstract $request = null, Zend_Controller_Response_Abstract $response = null)
    {
        if ($request === null) {
            if (PHP_SAPI == 'cli') {
                $argv = $_SERVER['argv'];
                if (isset($argv[1]) && $argv[1] == 'symfony') {
                    unset($argv[0]);
                    unset($argv[1]);
                    if (!in_array("--no-ansi", $argv)) $argv[] = '--ansi';
                    $cmd = './symfony/bin/console '.implode(' ', array_map('escapeshellarg', $argv));
                    passthru($cmd, $retVar);
                    exit($retVar);
                }
                $request = new Kwf_Controller_Request_Cli();
            } else {
                $request = new Kwf_Controller_Request_Http();
            }
        }
        if ($response === null) {
            $response = new Kwf_Controller_Response_Http();
        }
        try {
            $ret = parent::dispatch($request, $response);
        } catch (Zend_Controller_Router_Exception $e) {
            if ($e->getCode() == 404) {
                //fired by Zend_Controller_Router_Rewrite::route, transform into proper 404
                throw new Kwf_Exception_NotFound();
            } else {
                throw $e;
            }
        }

        Kwf_Benchmark::shutDown();
        return $ret;
    }
}

