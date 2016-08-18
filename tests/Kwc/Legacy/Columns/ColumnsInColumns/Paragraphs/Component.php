<?php
class Kwc_Legacy_Columns_ColumnsInColumns_Paragraphs_Component extends Kwc_Paragraphs_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['childModel'] = 'Kwc_Legacy_Columns_ColumnsInColumns_Paragraphs_TestModel';
        $ret['generators']['paragraphs']['component'] = array(
            'columns' => 'Kwc_Legacy_Columns_ColumnsInColumns_Columns_Component'
        );
        return $ret;
    }
}
