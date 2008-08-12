<?php
class Vpc_Posts_Post_Edit_Component extends Vpc_Posts_Write_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_Posts_Post_Edit_Form_Component';
        $ret['generators']['child']['component']['lastPosts'] = 'Vpc_Posts_Post_Edit_LastPosts_Component';
        return $ret;
    }
}
