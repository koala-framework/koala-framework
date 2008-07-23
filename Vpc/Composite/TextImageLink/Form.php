<?php
class Vpc_Composite_TextImageLink_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        $this->add(new Vps_Form_Field_TextField('title', trlVps('Title')));
        $this->add(new Vps_Form_Field_TextField('teaser', trlVps('Teaser')));

        $classes = Vpc_Abstract::getChildComponentClasses($class, 'child');
        foreach ($classes as $k=>$i) {
            $form = Vpc_Abstract_Form::createChildComponentForm($i, '-'.$k);
            $this->add($form);
        }
    }
}
