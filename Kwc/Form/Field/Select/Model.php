<?php
class Vpc_Form_Field_Select_Model extends Vps_Component_FieldModel
{
    protected function _init()
    {
        parent::_init();
        $this->_dependentModels['Values'] = new Vps_Model_FieldRows(array(
            'fieldName' => 'values'
        ));
    }
}