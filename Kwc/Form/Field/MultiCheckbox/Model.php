<?php
class Kwc_Form_Field_MultiCheckbox_Model extends Kwf_Component_FieldModel
{
    protected function _init()
    {
        parent::_init();
        $this->_dependentModels['Values'] = 'Kwc_Form_Field_MultiCheckbox_ValuesModel';
    }
}
