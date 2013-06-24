<?php
class Vpc_ParagraphsPassword_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Vps_Form_Field_TextField('password', trlVps('Password')));
    }
}
