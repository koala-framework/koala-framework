<?php
class Vps_Form_Cards_Foo extends Vps_Form_AddForm
{
    protected $_modelName = 'Vps_Form_Cards_FooModel';

    protected function _init()
    {
        parent::_init();
        $this->add(new Vps_Form_Field_TextField('lastname', 'Nachname'));

    }
}