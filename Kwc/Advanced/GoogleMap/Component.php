<?php
class Kwc_Advanced_GoogleMap_Component extends Kwc_Advanced_GoogleMapView_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlKwfStatic('Google Maps'),
            'ownModel' => 'Kwc_Advanced_GoogleMap_Model',
            'default' => array(
                'zoom' => 8,
                'height' => 300
            ),
        ));
        $ret['componentCategory'] = 'special';
        $ret['generators']['child']['component']['text'] = 'Kwc_Basic_Text_Component';
        $ret['placeholder']['noCoordinates'] = trlKwfStatic('coordinates not entered');
        $ret['assetsAdmin']['dep'][] = 'KwfGoogleMapField';
        return $ret;
    }

    protected function _getOptions()
    {
        $row = $this->_getRow();
        $fields = array('coordinates', 'zoom', 'width', 'height', 'zoom_properties',
                'scale', 'satelite', 'overview', 'routing', 'map_type', 'scrollwheel');
        foreach ($fields as $f) {
            $ret[$f] = $row->$f;
        }
        if (!isset($ret['coordinates'])) $ret['coordinates'] = '';
        return $ret;
    }
}
