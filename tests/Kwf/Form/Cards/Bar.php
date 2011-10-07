<?php
class Kwf_Form_Cards_Bar extends Kwf_Form_AddForm
{
    protected $_modelName = 'Kwf_Form_Cards_BarModel';

    protected function _init()
    {
        parent::_init();

        $this->add(new Kwf_Form_Field_TextField('firstname', 'Vorname'));

    }

}