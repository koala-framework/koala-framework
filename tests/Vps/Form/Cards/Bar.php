<?php
class Vps_Form_Cards_Bar extends Vps_Form_AddForm
{
    protected $_modelName = 'Vps_Form_Cards_BarModel';

    protected function _init()
    {
        parent::_init();

        $this->add(new Vps_Form_Field_TextField('firstname', 'Vorname'));

    }

}