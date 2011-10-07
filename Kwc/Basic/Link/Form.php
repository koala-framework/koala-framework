<?php
class Vpc_Basic_Link_Form extends Vpc_Abstract_Composite_Form
{
    protected $_createFieldsets = false;
    protected function _initFields()
    {
        $this->add(new Vps_Form_Field_TextField('text', trlVps('Linktext')))
            ->setWidth(300);

        parent::_initFields();
    }
}
