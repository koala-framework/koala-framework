<?php
class Kwc_FormDynamic_Basic_Form_Component extends Kwc_Form_Dynamic_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['paragraphs'] = 'Kwc_FormDynamic_Basic_Form_Paragraphs_Component';
        $ret['generators']['child']['component']['form'] = 'Kwc_FormDynamic_Basic_Form_Form_Component';
        $ret['ownModel'] = 'Kwc_FormDynamic_Basic_Form_TestModel';
        return $ret;
    }
}
