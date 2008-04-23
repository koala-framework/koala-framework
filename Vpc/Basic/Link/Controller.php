<?php
class Vpc_Basic_Link_Controller extends Vpc_Abstract_Composite_Controller
{
    protected function _initFields()
    {
        $this->_form->add(new Vps_Form_Field_TextField('text', trlVps('Text')))
            ->setWidth(300)
            ->setAllowBlank(false);

        parent::_initFields();
    }
}
