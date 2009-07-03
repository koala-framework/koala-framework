<?php
class Vpc_Mail_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $this->add(new Vps_Form_Field_TextField('subject', trlVps('Subject')))
            ->setAllowBlank(false)
            ->setWidth(300);
    }
}
