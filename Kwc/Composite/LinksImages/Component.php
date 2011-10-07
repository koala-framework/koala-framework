<?php
class Vpc_Composite_LinksImages_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Vpc_Composite_LinkImage_Component';
        $ret['componentName'] = trlVps('Links Images');
        $ret['componentIcon'] = new Vps_Asset('images');
        $ret['childModel'] = 'Vpc_Composite_LinksImages_Model';
        return $ret;
    }
}
