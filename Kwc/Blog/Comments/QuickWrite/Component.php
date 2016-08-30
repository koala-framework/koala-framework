<?php
class Kwc_Blog_Comments_QuickWrite_Component extends Kwc_Posts_QuickWrite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['form'] = 'Kwc_Blog_Comments_QuickWrite_Form_Component';
        $ret['plugins'] = array();
        return $ret;
    }
}
