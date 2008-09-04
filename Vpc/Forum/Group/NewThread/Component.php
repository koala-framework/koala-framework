<?php
class Vpc_Forum_Group_NewThread_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_Forum_Group_NewThread_Form_Component';
        return $ret;
    }
}
