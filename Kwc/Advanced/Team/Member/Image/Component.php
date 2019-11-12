<?php
class Kwc_Advanced_Team_Member_Image_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['dimensions'] = array(
            'default'=>array(
                'text' => trlKwfStatic('default'),
                'width' => 90,
                'height' => 120,
                'cover' => true,
            )
        );
        return $ret;
    }
}
