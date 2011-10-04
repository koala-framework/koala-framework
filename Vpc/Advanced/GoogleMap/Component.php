<?php
class Vpc_Advanced_GoogleMap_Component extends Vpc_Advanced_GoogleMapView_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Google Maps'),
            'ownModel' => 'Vpc_Advanced_GoogleMap_Model',
            'default' => array(
                'zoom' => 8,
                'height' => 300
            ),
        ));
        $ret['assetsAdmin']['dep'][] = 'VpsGoogleMapField';
        $ret['generators']['child']['component']['text'] = 'Vpc_Basic_Text_Component';
        $ret['placeholder']['noCoordinates'] = trlVps('coordinates not entered');
        return $ret;
    }

    protected function _getOptions()
    {
        $row = $this->_getRow();
        $fields = array('coordinates', 'zoom', 'width', 'height', 'zoom_properties',
                        'scale', 'satelite', 'overview', 'routing');
        foreach ($fields as $f) {
            $ret[$f] = $row->$f;
        }
        if (!isset($ret['coordinates'])) $ret['coordinates'] = '';
        return $ret;
    }
}
