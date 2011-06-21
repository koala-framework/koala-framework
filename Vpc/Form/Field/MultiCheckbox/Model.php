<?php
class Vpc_Form_Field_MultiCheckbox_Model extends Vps_Component_FieldModel
{
    protected function _init()
    {
        parent::_init();
        $this->_dependentModels['Values'] = 'Vpc_Form_Field_MultiCheckbox_ValuesModel';
    }
}
