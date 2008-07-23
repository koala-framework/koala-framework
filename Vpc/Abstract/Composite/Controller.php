<?php
class Vpc_Abstract_Composite_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected function _initFields()
    {
        $classes = Vpc_Abstract::getChildComponentClasses($this->class);
        foreach ($classes as $k=>$i) {
            $form = Vpc_Abstract_Form::createChildComponentForm($this->class, '-'.$k);
            $this->_form->add($form);
        }
    }
}
