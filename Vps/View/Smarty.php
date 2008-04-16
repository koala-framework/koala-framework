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

        $this->addScriptPath(VPS_PATH.'/views');
        $this->addScriptPath(getcwd().'/application/views');

        if (!isset($config['compile_dir'])) {
            $config['compile_dir'] = 'application/cache/views_c';
        }
        foreach ($config as $key => $value) {
            $this->_smarty->$key = $value;
        }
        $this->extTemplate = VPS_PATH . '/views/Ext.html';
        $this->_smarty->register_function("trl", "trl");
        $this->_smarty->register_function("trlc", "trlc");
        $this->_smarty->register_function("trlcp", "trlcp");
        $this->_smarty->register_function("trlp", "trlp");
        $this->_smarty->register_function("trlVps", "trlVps");
        $this->_smarty->register_function("trlcVps", "trlcVps");
        $this->_smarty->register_function("trlcpVps", "trlcpVps");
        $this->_smarty->register_function("trlpVps", "trlpVps");

        $this->config = Zend_Registry::get('config');
    }

    public function vpc($config)
    {
        throw new Vps_Exception("Noch nicht konvertiert, wenns benötigt wird niko sagen :D");
        $this->ext($config['class'], $config['config']);
    }

    public function ext($class, $config = array(), $viewport = null)
    {
        if (!is_string($class)) {
            throw new Vps_View_Exception('Class must be a string.');
        }

        $dep = new Vps_Assets_Dependencies('Admin');
        $jsFiles = $dep->getAssetFiles('js');
        $cssFiles = $dep->getAssetFiles('css');

        if (!$viewport) {
            $viewport = Zend_Registry::get('config')->ext->defaultViewport;
        }

        //das ist nötig weil wenn $config ein leeres Array ist, kommt sonst []
        //raus aber {} wird benötigt (array als config ist ungültig)
        $config = (object)$config;

        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ('_' != substr($key, 0, 1)) {
                $config->$key = $value;
            }
        }

        // View einrichten
        $ext['files']['js'] = $jsFiles;
        $ext['files']['css'] = $cssFiles;
        $ext['class'] = $class;
        $ext['config'] = Zend_Json::encode($config);
        $ext['viewport'] = $viewport;
        $ext['debug'] = Zend_Json::encode(!Zend_Registry::get('config')->debug->errormail);
        $ext['userRole'] = Zend_Registry::get('userModel')->getAuthedUserRole();
        $this->ext = $ext;
        $this->_renderFile = VPS_PATH.'/views/Master.html';
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
