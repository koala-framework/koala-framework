<?php
class Kwc_Events_Detail_Form extends Kwc_News_Detail_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        unset($this->fields['publish_date']);
        if (isset($this->fields['expiry_date'])) unset($this->fields['expiry_date']);
        $this->add(new Kwf_Form_Field_DateTimeField('start_date', trlKwf('From')))
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_DateTimeField('end_date', trlKwf('To')));
        $this->add(new Kwf_Form_Field_TextField('place', trlKwf('Place (City)')))
            ->setWidth(300);
    }
}
