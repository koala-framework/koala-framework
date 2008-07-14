<?php
class Vpc_Abstract_Composite_Form extends Vpc_Abstract_NonTableForm
{
    protected function _initFields()
    {
        parent::_initFields();
        $classes = Vpc_Abstract::getChildComponentClasses($this->getClass(), 'child');
        foreach ($classes as $key => $class) {
            $form = Vpc_Abstract_Form::createComponentForm($this->getClass(), "{0}-$class");

            $name = Vpc_Abstract::getSetting($this->getClass(), 'componentName');
            $name = str_replace('.', ' ', $name);
            $this->add(new Vps_Form_Container_FieldSet($name))
                ->add($form);
        }
    }
}
