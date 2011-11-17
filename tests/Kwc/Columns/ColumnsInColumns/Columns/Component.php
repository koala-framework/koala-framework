<?php
class Kwc_Columns_ColumnsInColumns_Columns_Component extends Kwc_Columns_Component
{
    public static function getSettings($parentComponentClass)
    {
        $ret = parent::getSettings($parentComponentClass);
        $ret['childModel'] = 'Kwc_Columns_ColumnsInColumns_Columns_ColumnsTestModel';
        $ret['ownModel'] = new Kwf_Model_FnF();
        return $ret;
    }
}
