<?php
class Vpc_Basic_Download_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        $classes = Vpc_Abstract::getSetting($class, 'childComponentClasses');
        $form = Vpc_Abstract_Form::createComponentForm('downloadTag', $classes['downloadTag']);
        $form->setIdTemplate('{0}-downloadTag');
        $this->add($form);

        $this->add(new Vps_Form_Field_TextField('infotext', trlVps('Link-Text')))
            ->setWidth(300);
    }
}
