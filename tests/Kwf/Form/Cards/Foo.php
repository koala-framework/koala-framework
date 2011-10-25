<?php
class Kwf_Form_Cards_Foo extends Kwf_Form_AddForm
{
    protected $_modelName = 'Kwf_Form_Cards_FooModel';

    protected function _init()
    {
        parent::_init();
        $this->add(new Kwf_Form_Field_TextField('lastname', 'Nachname'));

    }
}