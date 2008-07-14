<?php
class Vpc_Basic_Link_Form extends Vpc_Abstract_Composite_Form
{
    protected function _initFields()
    {
        $form = new Vpc_Abstract_Form('text', $this->getClass());
        $form->add(new Vps_Form_Field_TextField('text', trlVps('Linktext')))
            ->setWidth(300)
            ->setAllowBlank(false);

        parent::_initFields();
    }
}
