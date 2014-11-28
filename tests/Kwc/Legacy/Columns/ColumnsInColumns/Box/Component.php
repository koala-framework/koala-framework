<?php
class Kwc_Legacy_Columns_ColumnsInColumns_Box_Component extends Kwc_Basic_Html_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Legacy_Columns_ColumnsInColumns_Box_TestModel';
        return $ret;
    }
}
