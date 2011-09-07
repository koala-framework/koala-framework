<?php
class Vpc_Box_TitleEditable_Admin extends Vpc_Abstract_Admin
{
    public function getPagePropertiesForm()
    {
        $form = new Vpc_Abstract_Form(null, $this->_class);
        $form->add(new Vps_Form_Field_TextField('title', trlVps('Title')))
            ->setWidth(450);
        return $form;
    }
}
