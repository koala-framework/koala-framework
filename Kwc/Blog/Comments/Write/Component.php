<?php
class Kwc_Blog_Comments_Write_Component extends Kwc_Posts_Write_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['form'] = 'Kwc_Blog_Comments_QuickWrite_Form_Component';
        $ret['plugins'] = array();
        return $ret;
    }
}
