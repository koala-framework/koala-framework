<?php
class Vpc_FormDynamic_Basic_Form_Form_Component extends Vpc_Form_Dynamic_Form_Component
{
    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setModel(new Vps_Model_FnF());
    }
}
