<?php
class Kwc_Trl_Paragraphs_Paragraphs_Component extends Kwc_Paragraphs_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['paragraphs']['component'] = array(
            'child' => 'Kwc_Trl_Paragraphs_Paragraphs_Child_Component'
        );
        $ret['childModel'] = 'Kwc_Trl_Paragraphs_Paragraphs_TestModel';
        return $ret;
    }
}
