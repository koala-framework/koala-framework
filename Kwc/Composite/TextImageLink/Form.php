<?php
class Kwc_Composite_TextImageLink_Form extends Kwc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        $this->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
            ->setWidth(300);
        $this->add(new Kwf_Form_Field_TextField('teaser', trlKwf('Teaser')))
            ->setWidth(300);
        parent::_initFields();
    }
}
