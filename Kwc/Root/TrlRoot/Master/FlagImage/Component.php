<?php
class Kwc_Root_TrlRoot_Master_FlagImage_Component extends Kwc_Abstract_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions'] = array(
            'default'=>array(
                'text' => trlKwf('default'),
                'width' => 16,
                'height' => 16,
                'scale' => Kwf_Media_Image::SCALE_BESTFIT
            ),
        );
        $ret['componentName'] = trlcKwf('Flag of a Country', 'Flag');
        return $ret;
    }
}
