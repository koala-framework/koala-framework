<?php
class Vpc_Forum_Thread_Edit_Component extends Vpc_Posts_Detail_Edit_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_Forum_Thread_Edit_Form_Component';
        return $ret;
    }
}
