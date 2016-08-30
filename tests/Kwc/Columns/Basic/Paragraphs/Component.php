<?php
class Kwc_Columns_Basic_Paragraphs_Component extends Kwc_Paragraphs_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['childModel'] = 'Kwc_Columns_Basic_Paragraphs_TestModel';
        $ret['generators']['paragraphs']['component'] = array(
            'columns' => 'Kwc_Columns_Basic_Columns_Component'
        );
        return $ret;
    }
}
