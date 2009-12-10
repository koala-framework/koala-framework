<?php
class Vpc_Basic_Textfield_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Vps_Form_Field_TextField('content', trlVps('Content')))
            ->setWidth(400);
    }

}
