<?php
require_once 'Smarty/Smarty.class.php';

class Vps_View_Smarty extends Vps_View
{
    protected $_smarty;
    protected $_renderFile = 'Master.html';

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->_smarty = new Smarty();
        $this->_smarty->plugins_dir[] = 'SmartyPlugins/';

        $this->setScriptPath('application/views');
        if (!isset($config['compile_dir'])) {
            $config['compile_dir'] = 'application/views_c';
        }
        foreach ($config as $key => $value) {
            $this->_smarty->$key = $value;
        }
        $this->extTemplate = VPS_PATH . 'views/Ext.html';
    }

    public function ext($class, $config = array(), $viewport = null)
    {
        if ($class instanceof Vpc_Abstract) {
            if (!is_array($config)) { $config = array(); }
            $config = array_merge($config, $this->getConfig($class, array(), false));
            $class = $this->getClass($class);
        }
        if (!is_string($class)) {
            throw new Vps_View_Exception('Class must be a string.');
        }

        $dep = new Vps_Assets_Dependencies(Zend_Registry::get('config')->asset, 'application/config.ini', 'dependencies');
        if (Zend_Registry::get('config')->debug) {
            $jsFiles = $dep->getAssetFiles('js');
            $cssFiles = $dep->getAssetFiles('css');
        } else {
            $jsFiles = array('/assets/all.js');
            $cssFiles = array('/assets/all.css');
        }

        if (!$viewport) {
            if (Zend_Registry::get('config')->ext && Zend_Registry::get('config')->ext->defaultViewport) {
                $viewport = Zend_Registry::get('config')->ext->defaultViewport;
            } else {
                $viewport = 'Vps.Viewport';
            }
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

        $path = $this->getScriptPaths();

        //smarty needs a template_dir, and can only use templates,
        //found in that directory, so we have to strip it from the filename
        if ($this->getRenderFile() != '') {
            $file = $this->getRenderFile();
        } else {
            $file = substr(func_get_arg(0), strlen($path[0]));
        }

        //set the template diretory as the first directory from the path
        $this->_smarty->template_dir = $path[0];

        //process the template (and filter the output)
        $this->_smarty->display($file);
    }
}
