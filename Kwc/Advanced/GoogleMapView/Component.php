<?php
abstract class Kwc_Advanced_GoogleMapView_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assetsDefer']['dep'][] = 'KwfGoogleMap';
        $ret['assetsDefer']['dep'][] = 'ExtUtilJson';
        $ret['placeholder']['noCoordinates'] = ''; //leer, wird in GoogleMap benötgit damit man im backend sieht was falsch ist
        $ret['rootElementClass'] = 'kwfup-webStandard kwfup-webForm';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

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
        $defaults['zoomProperties'] = 0;
        $defaults['panControl'] = 1;
        $defaults['streetView'] = 1;
        $defaults['zoom'] = 10;
        $defaults['height'] = 400;
        $defaults['width'] = ''; // use 100% width, if no width is given
        $defaults['scale'] = 1;
        $defaults['satelite'] = 1;
        $defaults['overview'] = 1;
        $defaults['routing'] = 1;
        $defaults['autoOpenInfoWindow'] = 1;

        foreach ($defaults as $k=>$i) {
            if (!isset($options[$k])) {
                $options[$k] = $i;
            }
        }
        $ret['options'] = $options;

        // wird benötigt wenn gmap in switchDisplay liegt
        $ret['height'] = $options['height'];

        return $ret;
    }

    public function hasContent()
    {
        $options = $this->_getOptions();
        return !!$options['coordinates'];
    }

    abstract protected function _getOptions();
}
