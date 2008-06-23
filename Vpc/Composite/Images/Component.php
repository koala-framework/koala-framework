<?php
class Vpc_Composite_Images_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Images');
        $ret['componentIcon'] = new Vps_Asset('pictures');
        $ret['tablename'] = 'Vpc_Composite_Images_Model';
        $ret['childComponentClasses']['child'] = 'Vpc_Basic_Image_Component';
        return $ret;
    }
}
