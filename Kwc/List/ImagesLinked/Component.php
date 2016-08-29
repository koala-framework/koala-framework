<?php
class Kwc_List_ImagesLinked_Component extends Kwc_List_Images_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component'] = 'Kwc_Composite_LinkImage_Component';
        $ret['componentName'] = trlKwfStatic('Linked Images');
        $ret['componentIcon'] = 'images';
        return $ret;
    }
}
