<?php
class Vpc_Events_Detail_Form extends Vpc_News_Detail_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        unset($this->fields['publish_date']);
        if (isset($this->fields['expiry_date'])) unset($this->fields['expiry_date']);
        $this->add(new Vps_Form_Field_DateTimeField('start_date', trlVps('From')))
            ->setAllowBlank(false);
        $this->add(new Vps_Form_Field_DateTimeField('end_date', trlVps('To')));
        $this->add(new Vps_Form_Field_TextField('place', trlVps('Place (City)')))
            ->setWidth(300);
    }
}
