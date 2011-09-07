<?php
class Vpc_Box_TitleEditable_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Vps_Form_Field_TextField('title', trlVps('Title')))
            ->setWidth(450);
    }
}
