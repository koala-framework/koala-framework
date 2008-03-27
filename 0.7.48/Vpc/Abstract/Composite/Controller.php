<?php
class Vpc_Abstract_Composite_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected function _initFields()
    {
        $classes = Vpc_Abstract::getSetting($this->class, 'childComponentClasses');
        foreach ($classes as $k=>$i) {
            $c = Vpc_Admin::getComponentFile($i, 'Form', 'php', true);
            $form = new $c($i);
            $form->setComponentIdTemplate('{0}-'.$k);
            $this->_form->add($form);
        }
    }
}
