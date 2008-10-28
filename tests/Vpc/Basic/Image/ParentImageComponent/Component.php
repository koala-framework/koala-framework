<?php
class Vpc_Basic_Image_ParentImageComponent_Component extends Vpc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vpc_Basic_Image_TestModel';
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Basic_Image_ParentImageComponent_Child_Component'
        );
        $ret['dimensions'] = array(100, 100, Vps_Media_Image::SCALE_DEFORM);
        return $ret;
    }
}
