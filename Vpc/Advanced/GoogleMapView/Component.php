<?php
abstract class Vpc_Advanced_GoogleMapView_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['files'][] = 'vps/Vpc/Advanced/GoogleMapView/Component.js';
        $ret['assets']['dep'][] = 'ExtCore';
        $ret['assets']['dep'][] = 'VpsGoogleMap';
        $ret['assets']['dep'][] = 'ExtUtilJson';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $options = $this->_getOptions();
        $pos = strpos($options['coordinates'], ";");
        $options['longitude'] = substr($options['coordinates'], 0, $pos);
        $options['latitude'] = substr($options['coordinates'], $pos + 1, strlen($this->_getRow()->coordinates) - 1);
        $options['coordinates'] = str_replace(';', ',', $options['coordinates']);

        $ret['options'] = Zend_Json::encode($options);

        // wird benötigt wenn gmap in switchDisplay liegt
        $ret['height'] = $options['height'];

        return $ret;
    }

    abstract protected function _getOptions();
}
