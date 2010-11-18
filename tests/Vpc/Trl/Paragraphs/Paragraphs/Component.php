<?php
class Vpc_Trl_Paragraphs_Paragraphs_Component extends Vpc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['paragraphs']['component'] = array(
            'child' => 'Vpc_Trl_Paragraphs_Paragraphs_Child_Component'
        );
        $ret['childModel'] = 'Vpc_Trl_Paragraphs_Paragraphs_TestModel';
        return $ret;
    }
}
