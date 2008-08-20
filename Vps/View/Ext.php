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
        $ext['config'] = Zend_Json::encode($config);
        $ext['viewport'] = $viewport;
        $ext['userRole'] = Zend_Registry::get('userModel')->getAuthedUserRole();
        $this->ext = $ext;
        $this->extTemplate = 'ext.tpl';

        $this->applicationName = Zend_Registry::get('config')->application->name;
    }
}
