<?php
class Kwc_ParagraphsPassword_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_TextField('password', trlKwf('Password')));
    }
}
