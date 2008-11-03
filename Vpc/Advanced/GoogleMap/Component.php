<?php
class Vpc_Advanced_GoogleMap_Component extends Vpc_Advanced_GoogleMapView_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => 'Google Maps',
            'tablename' => 'Vpc_Advanced_GoogleMap_Model',
            'default' => array(
                'zoom' => 8,
                'height' => 300
            ),
        ));
        $ret['assetsAdmin']['dep'][] = 'VpsGoolgeMapField';
        $ret['generators']['child']['component']['text'] = 'Vpc_Basic_Text_Component';
        $ret['placeholder']['noCoordinates'] = trlVps('coordinates not entered');
        return $ret;
    }

    protected function _getOptions()
    {
        return $this->_getRow()->toArray();
    }
}
