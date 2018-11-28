<?php
class Kwc_Basic_LinkTag_Phone_Form extends Kwc_Abstract_Form
{
    public function _initFields()
    {
        parent::_initFields();

        $this->add(new Kwf_Form_Field_TextField('phone', trlKwf('Phone')))
            ->setWidth(300);
    }
}
