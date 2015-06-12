<?php
class Kwc_Advanced_GoogleMapView_TestComponent extends Kwc_Advanced_GoogleMapView_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }
    protected function _getOptions()
    {
        $id = $this->getData()->componentId;
        if ($id == '2000') {
            return array();
        } else if ($id == '2001') {
            return array('coordinates' => '');
        } else if ($id == '2002') {
            return array('coordinates' => '12;13');
        } else if ($id == '2003') {
            return array(
                'coordinates' => '12;13',
                'routing' => 0,
                'zoom' => 11,
                'zoomProperties' => 1
            );
        }
    }
}
