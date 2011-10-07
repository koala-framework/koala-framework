<?php
class Vps_Component_SharedData_Detail_SharedData_Form extends Vpc_Abstract_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Vps_Form_Field_TextField('text', 'Text'));
    }
}