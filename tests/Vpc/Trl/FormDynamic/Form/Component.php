<?php
class Vpc_Trl_FormDynamic_Form_Component extends Vpc_Form_Dynamic_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['paragraphs'] = 'Vpc_Trl_FormDynamic_Form_Paragraphs_Component';
        $ret['ownModel'] = 'Vpc_Trl_FormDynamic_Form_TestModel';
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setModel(new Vps_Model_FnF());
    }
}
