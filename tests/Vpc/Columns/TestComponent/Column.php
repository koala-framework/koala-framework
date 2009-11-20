<?php
class Vpc_Columns_TestComponent_Column extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'test component';
        $ret['ownModel'] = 'Vpc_Columns_TestComponent_Column_Model';
        return $ret;
    }

}
