<?php
class Kwc_Advanced_GoogleMapViewManual_TestComponent extends Kwc_Advanced_GoogleMapView_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }
    protected function _getOptions()
    {
        return array(
            'coordinates' => '47.8978146;13.1877709'
        );
//                'routing' => 0,
//                'zoom' => 11,
//                'zoomProperties' => 1
    }
}
