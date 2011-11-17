<?php
class Kwc_Columns_Basic_TestComponent_Column extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'test component';
        $ret['ownModel'] = 'Kwc_Columns_Basic_TestComponent_Column_Model';
        return $ret;
    }

}
