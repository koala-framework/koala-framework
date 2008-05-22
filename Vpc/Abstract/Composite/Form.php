<?php
class Vpc_Abstract_Composite_Form extends Vps_Form_NonTableForm
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        $classes = Vpc_Abstract::getSetting($class, 'childComponentClasses');
        foreach ($classes as $k=>$i) {
            $c = Vpc_Admin::getComponentFile($i, 'Form', 'php', true);
            $form = new $c($i, $i);
            $form->setComponentIdTemplate('{0}-'.$k);
            $this->add($form);
        }
    }
}
