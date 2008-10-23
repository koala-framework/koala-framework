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
        return $ret;
    }

    protected function _getOptions()
    {
        $ret = array();
        $ret['zoom_properties'] = $this->_getRow()->zoom_properties;
        $ret['zoom'] = $this->_getRow()->zoom;
        $ret['height'] = $this->_getRow()->height;
        $ret['width'] = $this->_getRow()->width;
        $ret['coordinates'] = $this->_getRow()->coordinates;
        $ret['scale'] = $this->_getRow()->scale;
        $ret['satelite'] = $this->_getRow()->satelite;
        $ret['overview'] = $this->_getRow()->overview;
        return $ret;
    }

    public function hasContent()
    {
        $options = $this->_getOptions();
        return !!$options['coordinates'];
    }
}
