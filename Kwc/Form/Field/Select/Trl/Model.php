<?php
class Kwc_Form_Field_Select_Trl_Model extends Kwf_Component_FieldModel
{
    protected function _init()
    {
        parent::_init();
        $this->_dependentModels['Values'] = new Kwf_Model_FieldRows(array(
            'fieldName' => 'values'
        ));
    }
}