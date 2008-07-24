<?php
class Vpc_Composite_TextImageLink_Form extends Vpc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        $this->add(new Vps_Form_Field_TextField('title', trlVps('Title')));
        $this->add(new Vps_Form_Field_TextField('teaser', trlVps('Teaser')));
        parent::_initFields();
    }
}
