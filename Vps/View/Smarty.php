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

        $this->setScriptPath('../application/views');
        if (!isset($config['compile_dir'])) {
            $config['compile_dir'] = '../application/views_c';
        }
        foreach ($config as $key => $value) {
            $this->_smarty->$key = $value;
        }
        $this->extTemplate = VPS_PATH . '/views/Ext.html';
    }
    
    public function ext($class, $config = array(), $renderTo = '', $files = array())
    {
        if (!is_string($class)) {
            throw new Vps_View_Exception('Class must be a string.');
        }
        
        // Pfade zu den Files hinzufügen
        if (preg_match('#/www/usr/([0-9a-z]+)/#', __FILE__, $m)) {
            $user = $m[1];
        } else if (substr(__FILE__, strlen('/www/public/')) == '/www/public/') {
            $user = 'vivid';
        } else {
            $user = 'production';
        }

        // Ext-Pfad
        $cfg = new Zend_Config_Ini('../application/config.ini', $user);
        $vpsPath = $cfg->path->vps->http;
        if (isset($cfg->path->ext->http)) {
            $extPath = $cfg->path->ext->http;
        } else {
            $extPath = $cfg->path->vps->http . '/files/ext';
        }
        
        if (is_array($this->ext['files'])) {
            $files = array_merge($this->ext['files'], $files);
        }
        foreach ($files as $x => $file) {
            $files[$x] = $vpsPath . $file;
        }

        if (is_array($this->ext['config'])) {
            $config = array_merge($this->ext['config'], $config);
        }

        // View einrichten
        $ext['files'] = $files;
        $ext['class'] = isset($this->ext['class']) ? $this->ext['class'] : $class;
        $ext['vpsPath'] = $vpsPath;
        $ext['extPath'] = $extPath;
        $ext['config'] = Zend_Json::encode($config);
        $ext['renderTo'] = $renderTo;
        $this->ext = $ext;
    }
    
    public function vpc($config = array())
    {
        // View einrichten
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
