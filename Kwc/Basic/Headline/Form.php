<?php
class Kwc_Basic_Headline_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Kwf_Form_Field_TextField('headline1', trlKwf('Headline')))
            ->setWidth(450)
            ->setAllowBlank(false);
    }
}
