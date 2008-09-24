<?php
class Vpc_Events_Detail_Form extends Vpc_News_Detail_Abstract_Form
{
    protected _initFields()
    {
        parent::_initFields();
        $this->add(new Vps_Form_Field_DateField('start_date', trlVps('From')));
        $this->add(new Vps_Form_Field_DateField('end_date', trlVps('To')));
        $this->add(new Vps_Form_Field_TextField('place', trlVps('Place (City)'));
    }
}
