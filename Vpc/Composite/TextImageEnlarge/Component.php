<?php
class Vpc_Composite_TextImageEnlarge_Component extends Vpc_Composite_TextImage_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Text Image enlarge');
        $ret['generators']['child']['component']['image'] =
                                        'Vpc_Basic_Image_Enlarge_Component';
        return $ret;
    }
}
