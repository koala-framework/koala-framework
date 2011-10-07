<?php
class Vpc_Cc_Paragraphs_Master_Paragraphs_Component extends Vpc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Vpc_Cc_Paragraphs_Master_Paragraphs_TestModel';
        $ret['generators']['paragraphs']['component'] = array(
            'simple' => 'Vpc_Cc_Paragraphs_Master_Paragraphs_Simple_Component'
        );
        return $ret;
    }
}
