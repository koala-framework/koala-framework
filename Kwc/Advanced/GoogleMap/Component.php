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
        $ret['assetsAdmin']['dep'][] = 'KwfGoogleMapField';
        $ret['generators']['child']['component']['text'] = 'Kwc_Basic_Text_Component';
        $ret['placeholder']['noCoordinates'] = trlKwfStatic('coordinates not entered');
        return $ret;
    }

    protected function _getOptions()
    {
        $row = $this->_getRow();
        $ret = array();
        $ret['coordinates'] = $row->coordinates;
        $ret['zoom'] = $row->zoom;
        $ret['width'] = $row->width;
        $ret['height'] = $row->height;
        $ret['zoomProperties'] = $row->zoom_properties;
        $ret['scale'] = $row->scale;
        $ret['satelite'] = $row->satelite;
        $ret['overview'] = $row->overview;
        $ret['routing'] = $row->routing;
        $ret['mapType'] = $row->map_type;
        if (!isset($ret['coordinates'])) $ret['coordinates'] = '';
        return $ret;
    }
}
