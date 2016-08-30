<?php
class Kwc_Trl_FormDynamic_Form_Paragraphs_Component extends Kwc_Form_Dynamic_Paragraphs_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['childModel'] = 'Kwc_Trl_FormDynamic_Form_Paragraphs_TestModel';
        $ret['generators']['paragraphs']['component'] = array(
            'textField' => 'Kwc_Trl_FormDynamic_Form_Paragraphs_TextField_Component',
        );
        return $ret;
    }
}