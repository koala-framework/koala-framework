<?php
class Vps_View_Ext extends Vps_View
{
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
        $ext['userRole'] = Zend_Registry::get('userModel')->getAuthedUserRole();
        $this->ext = $ext;
        $this->extTemplate = 'ext.tpl';

        $this->debug = array(
            'js'  => !!Zend_Registry::get('config')->debug->assets->js,
            'css' => !!Zend_Registry::get('config')->debug->assets->css,
            'autoClearCache' => false,
            'menu' => !!Zend_Registry::get('config')->debug->menu,
            'querylog' => !!Zend_Registry::get('config')->debug->querylog,
            'displayErrors' => !Zend_Registry::get('config')->debug->errormail,
            'requestNum' => Zend_Registry::get('requestNum'),
            'template' => 'debug.tpl'
        );
        $sessionAssets = new Zend_Session_Namespace('debugAssets');
        if (isset($sessionAssets->js)) {
            $this->debug['js'] = $sessionAssets->js;
        }
        if (isset($sessionAssets->css)) {
            $this->debug['css'] = $sessionAssets->css;
        }
        if (isset($sessionAssets->autoClearCache)) {
            $this->debug['autoClearCache'] = $sessionAssets->autoClearCache;
        }
    }
}
