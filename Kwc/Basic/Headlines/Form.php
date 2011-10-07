<?php
class Kwc_Basic_Headlines_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Kwf_Form_Field_TextField('headline1', trlKwf('Headline 1')))
            ->setWidth(450)
            ->setAllowBlank(false);
        $this->fields->add(new Kwf_Form_Field_TextField('headline2', trlKwf('Headline 2')))
            ->setWidth(450);
    }
}
