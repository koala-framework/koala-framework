<?php
class Kwc_Basic_Anchor_Form extends Kwc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Kwf_Form_Field_TextField('anchor', trlKwf('Name')))
            ->setMaxLength(50)
            ->setAllowBlank(false);
    }
}
