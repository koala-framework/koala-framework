<?php
class Kwc_Posts_QuickWrite_Component extends Kwc_Posts_Write_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['preview'] = null;
        $ret['generators']['child']['component']['lastPosts'] = null;
        $ret['generators']['child']['component']['form'] = 'Kwc_Posts_QuickWrite_Form_Component';
        $ret['plugins'] = array();
        return $ret;
    }
}
