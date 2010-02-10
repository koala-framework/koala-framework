<?php
class Vpc_Trl_Paragraphs_Paragraphs_Trl_Component extends Vpc_Paragraphs_Trl_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Vpc_Trl_Paragraphs_Paragraphs_Trl_TestModel';
        return $ret;
    }
}
