<?php
class Vpc_Abstract_Composite_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $classes = Vpc_Abstract::getChildComponentClasses($this->getClass(), 'child');
        foreach ($classes as $key => $class) {
            if (!$class) continue;
            $form = Vpc_Abstract_Form::createChildComponentForm($this->getClass(), "-$key");
            if ($form) {
                $name = Vpc_Abstract::getSetting($class, 'componentName');
                $name = str_replace('.', ' ', $name);
                $this->add(new Vps_Form_Container_FieldSet($name))
                    ->add($form);
            }
        }
    }
}
