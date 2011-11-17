<?php
class Kwc_Columns_Basic_TestComponent_Column_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_TextField('foo', 'Foo'));
    }
}
