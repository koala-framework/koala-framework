<?php
class Kwc_Basic_Image_Crop_Root extends Kwc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_Image_Crop_ImageComponent'
        );
        return $ret;
    }
}
