<?php
class Kwc_Blog_Comments_QuickWrite_Component extends Kwc_Posts_QuickWrite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Kwc_Blog_Comments_QuickWrite_Form_Component';
        $ret['plugins'] = array();
        return $ret;
    }
}
