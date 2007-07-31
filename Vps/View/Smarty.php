<?php
require_once 'Smarty/Smarty.class.php';

class Vps_View_Smarty extends Zend_View_Abstract
{
    protected $_smarty;
    protected $_renderFile = 'Master.html';
    public $ext = array();

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
    
    public function setExtConfig($param, $value) {
        $this->ext['config'][$param] = $value;
    }
    
    public function ext($class, $config = array(), $renderTo = '')
    {
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
        if (isset($this->ext['config']) && is_array($this->ext['config'])) {
            $config = array_merge($this->ext['config'], $config);
        }
        
        if ($class == '' && isset($this->ext['class'])) {
            $class = $this->ext['class'];
        }

        // View einrichten
        $ext['files']['js'] = $jsFiles;
        $ext['files']['css'] = $cssFiles;
        $ext['class'] = $class;
        $ext['config'] = Zend_Json::encode($config);
        $ext['renderTo'] = $renderTo;
        $this->ext = $ext;
    }
    
    public function vpc($config = array())
    {
        $this->ext('', $config);
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
