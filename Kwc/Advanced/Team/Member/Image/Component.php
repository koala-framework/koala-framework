<?php
class Kwc_Advanced_Team_Member_Image_Component extends Kwc_Abstract_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions'] = array(
            'default'=>array(
                'text' => trlKwf('default'),
                'width' => 90,
                'height' => 120,
                'bestfit' => false,
            )
        );
        return $ret;
    }
}
