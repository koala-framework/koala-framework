<?php
class Vpc_Form_Dynamic_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Form');
        $ret['componentIcon'] = new Vps_Asset('application_form');
        $ret['generators']['child']['component']['paragraphs'] = 'Vpc_Form_Dynamic_Paragraphs_Component';
        $ret['generators']['child']['component']['form'] = 'Vpc_Form_Dynamic_Form_Component';
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        $ret['editComponents'] = array('paragraphs');
        return $ret;
    }
}
