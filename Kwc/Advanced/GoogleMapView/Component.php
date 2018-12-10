<?php
abstract class Kwc_Advanced_GoogleMapView_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['noCoordinates'] = ''; //leer, wird in GoogleMap benötgit damit man im backend sieht was falsch ist
        $ret['rootElementClass'] = 'kwfUp-webStandard kwfUp-webForm';
        $ret['optimizedMobileUI'] = false;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);

        $options = $this->_getOptions();
        if (!isset($options['coordinates'])) {
            throw new Kwf_Exception("You must return coordinates in _getOptions");
        }
        $split = explode(';', $options['coordinates']);
        if (count($split) == 2) {
            $options['latitude'] = $split[0];
            $options['longitude'] = $split[1];
        } else {
            $options['latitude'] = '';
            $options['longitude'] = '';
        }
        $options['coordinates'] = str_replace(';', ',', $options['coordinates']);

        $defaults = array();
        $defaults['zoomControl'] = true;
        $defaults['streetViewControl'] = false;
        $defaults['zoom'] = 10;
        $defaults['height'] = 400;
        $defaults['width'] = ''; // use 100% width, if no width is given
        $defaults['routing'] = true;
        $defaults['autoOpenInfoWindow'] = true;
        $defaults['mapTypeControl'] = true;
        $defaults['singleMarkerZoom'] = null;
        $defaults['clickableIcons'] = false;

        foreach ($defaults as $k=>$i) {
            if (!isset($options[$k])) {
                $options[$k] = $i;
            }
        }
        $ret['options'] = $options;

        // wird benötigt wenn gmap in switchDisplay liegt
        $ret['height'] = $options['height'];

        $ret['optimizedMobileUI'] = $this->_getSetting('optimizedMobileUI');

        return $ret;
    }

    public function hasContent()
    {
        $options = $this->_getOptions();
        return !!$options['coordinates'];
    }

    abstract protected function _getOptions();
}
