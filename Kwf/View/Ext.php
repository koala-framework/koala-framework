<?php
class Kwf_View_Ext extends Kwf_View
{
    public function render($name)
    {
        if (isset($this->xtype) && !isset($this->ext)) {
            $this->ext(null);
        }
        return parent::render($name);
    }

    public function kwc($config)
    {
        $this->ext(null, $config);
    }

    public function ext($class, $config = array(), $viewport = null)
    {
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
        $loader = new Kwf_Assets_Loader();
        $dep = $loader->getDependencies();
        $ext['class'] = $class;
        if (!isset($config->id)) $config->id = 'mainPanel';
        if (!isset($config->region)) $config->region = 'center';
        if (isset($config->assetsType)) {
            $ext['assetsType'] = $config->assetsType;
            unset($config->assetsType);
        } else {
            $ext['assetsType'] = 'Admin';
        }
        $ext['config'] = $config;

        if (!$viewport) {
            if (isset($config->viewport)) {
                $viewport = $config->viewport;
            } else {
                $viewport = Zend_Registry::get('config')->ext->defaultViewport;
            }
        }
        $ext['viewport'] = $viewport;

        $ext['userRole'] = Zend_Registry::get('userModel')->getAuthedUserRole();
        $this->ext = $ext;
        $this->extTemplate = 'ext.tpl';
        if (Kwf_Util_SessionToken::getSessionToken()) {
            $this->sessionToken = Kwf_Util_SessionToken::getSessionToken();
        }

        $this->applicationName = Zend_Registry::get('config')->application->name;

        if (Kwf_Registry::get('config')->ext->favicon) {
            $this->favicon = Kwf_Registry::get('config')->ext->favicon;
        } else if (file_exists('images/favicon.ico')) {
            $ico = new Kwf_Asset('images/favicon.ico', 'web');
            $fx = Kwf_Registry::get('config')->ext->faviconFx;
            if (!$fx) $fx = array();
            else if (is_string($fx)) $fx = array($fx);
            $this->favicon = $ico->toString($fx);
        } else {
            $this->favicon = null;
        }
    }
}
