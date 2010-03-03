<?php
class Vpc_Root_TrlRoot_Master_FlagImage_Component extends Vpc_Abstract_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions'] = array(
            'default'=>array(
                'text' => trlVps('default'),
                'width' => 16,
                'height' => 16,
                'scale' => Vps_Media_Image::SCALE_BESTFIT
            ),
        );
        $ret['componentName'] = trlVps('Flag');
        return $ret;
    }
}
