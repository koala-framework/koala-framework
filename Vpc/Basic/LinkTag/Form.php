<?php
class Vpc_Basic_LinkTag_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $classes = Vpc_Abstract::getChildComponentClasses($class, 'link');

        reset($classes);
        $cards = $this->add(new Vps_Form_Container_Cards('component', trlVps('Linktype')))
            ->setDefaultValue(key($classes));

        foreach ($classes as $name => $class) {
            $form = Vpc_Abstract_Form::createChildComponentForm($this->getClass(), '-link');
            $form->setAutoHeight(true);
            $form->setBaseCls('x-plain');

            $card = $cards->add();
            $title = Vpc_Abstract::getSetting($class, 'componentName');
            $title = str_replace('.', ' ', $title);
            $card->setTitle($title);
            $card->setName($name);
            $card->add($form);
        }
    }
}
