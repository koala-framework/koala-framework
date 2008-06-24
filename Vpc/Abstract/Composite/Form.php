<?php
class Vpc_Abstract_Composite_Form extends Vps_Form_NonTableForm
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        $classes = Vpc_Abstract::getSetting($class, 'childComponentClasses');
        foreach ($classes as $k=>$i) {
            $form = Vpc_Abstract_Form::createComponentForm($k, $i);
            $form->setIdTemplate('{0}-'.$k);

            $name = Vpc_Abstract::getSetting($i, 'componentName');
            $name = str_replace('.', ' ', $name);
            $this->add(new Vps_Form_Container_FieldSet($name))
                ->add($form);
        }
    }
}
