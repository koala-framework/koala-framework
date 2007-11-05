<?php
require_once 'Smarty.class.php';

class Vps_View_Smarty extends Zend_View_Abstract
{
    protected $_smarty;
    protected $_renderFile = 'Master.html';

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->_smarty = new Smarty();
        $this->_smarty->plugins_dir[] = 'SmartyPlugins';

        $this->addScriptPath('application/views');
        $this->addScriptPath(VPS_PATH.'/views');

        if (!isset($config['compile_dir'])) {
            $config['compile_dir'] = 'application/views_c';
        }
        foreach ($config as $key => $value) {
            $this->_smarty->$key = $value;
        }
        $this->extTemplate = VPS_PATH . '/views/Ext.html';

        $this->config = Zend_Registry::get('config');
    }

    public function ext($class, $config = array(), $viewport = null)
    {
        if ($class instanceof Vpc_Abstract) {
            if (!is_array($config)) { $config = array(); }
            $admin = Vpc_Admin::getInstance($class);
            $adminConfig = $admin->getConfig($class, array(), false);
            $config = array_merge($config, $adminConfig);
            $class = $admin->getControllerClass();
        }
        if (!is_string($class)) {
            throw new Vps_View_Exception('Class must be a string.');
        }

        $dep = new Vps_Assets_Dependencies(Zend_Registry::get('config'));
        if (Zend_Registry::get('config')->debug->assets) {
            $jsFiles = $dep->getAssetFiles('js');
            $cssFiles = $dep->getAssetFiles('css');
        } else {
            $jsFiles = array('/assets/all.js');
            $cssFiles = array('/assets/all.css');
        }

        if (!$viewport) {
            $viewport = Zend_Registry::get('config')->ext->defaultViewport;
        }

        //das ist nötig weil wenn $config ein leeres Array ist, kommt sonst []
        //raus aber {} wird benötigt (array als config ist ungültig)
        $config = (object)$config;

        // View einrichten
        $ext['files']['js'] = $jsFiles;
        $ext['files']['css'] = $cssFiles;
        $ext['class'] = $class;
        $ext['config'] = Zend_Json::encode($config);
        $ext['viewport'] = $viewport;
        $ext['debug'] = Zend_Json::encode(!Zend_Registry::get('config')->debug->errormail);
        $userRole = Zend_Auth::getInstance()->getStorage()->read();
        $ext['userRole'] = $userRole ? $userRole->role : 'guest';
        $this->ext = $ext;
    }

    public function setRenderFile($renderFile)
    {
        $this->_renderFile = $renderFile;
    }

    public function getRenderFile()
    {
        return $this->_renderFile;
    }

    public function getEngine()
    {
        return $this->_smarty;
    }

    public function setCompilePath($path)
    {
        $this->_smarty->compile_dir = $path;
    }

    protected function _run()
    {
        $this->strictVars(true);

        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ('_' != substr($key, 0, 1)) {
                $this->_smarty->assign($key, $value);
            }
        }

        //why 'this'?
        //to emulate standard zend view functionality
        //doesn't mess up smarty in any way
        $this->_smarty->assign_by_ref('this', $this);

        //smarty needs a template_dir, and can only use templates,
        //found in that directory, so we have to strip it from the filename
        if ($this->getRenderFile() != '') {
            $file = $this->getRenderFile();
            foreach ($this->getScriptPaths() as $path) {
                if (file_exists($path.$file)) {
                    $this->_smarty->template_dir = $path;
                    break;
                }
            }
        } else {
            throw new Vps_Exception("Not Implemented");
            //$file = substr(func_get_arg(0), strlen($path));
        }

        //process the template (and filter the output)
        $this->_smarty->display($file);
    }
}
