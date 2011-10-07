<?php
class Kwc_Basic_Html_ChildComponent_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_TextField('aa', 'Aa'));
    }
}
