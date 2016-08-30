<?php
class Kwc_ImageResponsive_InScrollDiv_Components_Image_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_ImageResponsive_InScrollDiv_Components_Image_TestModel';
        $ret['dimensions'] = array(
            'default'=>array(
                'width' => 300,
                'height' => 200,
                'cover' => true
           )
        );
        return $ret;
    }
}
