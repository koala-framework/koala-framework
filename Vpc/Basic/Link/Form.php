<?php
class Vpc_Basic_Link_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);

        $this->add(new Vps_Form_Field_TextField('text', trlVps('Linktext')))
            ->setWidth(300)
            ->setAllowBlank(false);

        $classes = Vpc_Abstract::getSetting($class, 'childComponentClasses');
        $form = Vpc_Abstract_Form::createComponentForm('linkTag', $classes['linkTag']);
        $form->setIdTemplate('{0}-linkTag');
        $this->add($form);
    }
}
