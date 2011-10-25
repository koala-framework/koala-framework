<?php
class Kwf_Form_Container_FieldSet_Form extends Kwf_Form
{
    protected function _init()
    {
        parent::_init();
        $this->add(new Kwf_Form_Field_TextField('title', trlKwf('Label')))
            ->setWidth(200);
    }
}
