<?php
class Kwc_Composite_LinksImages_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Kwc_Composite_LinkImage_Component';
        $ret['componentName'] = trlKwf('Links Images');
        $ret['componentIcon'] = new Kwf_Asset('images');
        $ret['childModel'] = 'Kwc_Composite_LinksImages_Model';
        return $ret;
    }
}
