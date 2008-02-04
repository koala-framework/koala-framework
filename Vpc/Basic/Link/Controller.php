<?php
class Vpc_Basic_Link_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    public function _initFields()
    {
        $classes = Vpc_Abstract::getSetting($this->class, 'childComponentClasses');

        $this->_form->add(new Vps_Auto_Field_TextField('text', 'Text'))
            ->setWidth(300)
            ->setAllowBlank(false);

        $form = new Vpc_Basic_LinkTag_Form($classes['linkTag']);
        $form->setComponentIdTemplate('{0}-tag');
        $this->_form->add($form);
    }
}
