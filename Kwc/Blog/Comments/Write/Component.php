<?php
class Kwc_Blog_Comments_Write_Component extends Kwc_Posts_Write_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Kwc_Blog_Comments_QuickWrite_Form_Component';
        $ret['plugins'] = array();
        return $ret;
    }
}
