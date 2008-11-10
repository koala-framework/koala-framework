<?php
class Vps_View_Ext extends Vps_View
{
    public function vpc($config)
    {
        $this->ext(null, $config);
    }

    public function ext($class, $config = array(), $viewport = null)
    {
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
        $dep = new Vps_Assets_Dependencies();
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
        $ext['viewport'] = $viewport;
        $ext['userRole'] = Zend_Registry::get('userModel')->getAuthedUserRole();
        $this->ext = $ext;
        $this->extTemplate = 'ext.tpl';

        $this->applicationName = Zend_Registry::get('config')->application->name;
    }
}
