<?php
class Vpc_Composite_Images_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Images');
        $ret['componentIcon'] = new Vps_Asset('pictures');
        $ret['generators']['child']['component'] = 'Vpc_Basic_Image_Component';
        $ret['pdfColumns'] = 1;
        return $ret;
    }
}
