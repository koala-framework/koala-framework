<?php
class Kwf_Component_SharedData_Detail_SharedData_Form extends Kwc_Abstract_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Kwf_Form_Field_TextField('text', 'Text'));
    }
}