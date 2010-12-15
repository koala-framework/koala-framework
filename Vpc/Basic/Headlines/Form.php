<?php
class Vpc_Basic_Headlines_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Vps_Form_Field_TextField('headline1', trlVps('Headline 1')))
            ->setWidth(450)
            ->setAllowBlank(false);
        $this->fields->add(new Vps_Form_Field_TextField('headline2', trlVps('Headline 2')))
            ->setWidth(450);
    }
}
