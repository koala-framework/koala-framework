<?php
class Vpc_Basic_LinkTag_Form extends Vpc_Abstract_Form
{
    protected function _init()
    {
        parent::_init();

        $gen = Vpc_Abstract::getSetting($this->getClass(), 'generators');
        $classes = $gen['link']['component'];
        $cards = $this->add(new Vps_Form_Container_Cards('component', trlVps('Link-Action')))
            ->setDefaultValue(key($classes));

        foreach ($classes as $name => $class) {
            $form = Vpc_Abstract_Form::createChildComponentForm($this->getClass(), '-' . $name, $name);
            if ($form) {
                $form->setIdTemplate('{0}-link');
                $form->setAutoHeight(true);
                $form->setBaseCls('x-plain');
            }

            $card = $cards->add();
            $title = Vpc_Abstract::getSetting($class, 'componentName');
            $title = str_replace('.', ' ', $title);
            $card->setTitle($title);
            $card->setName($name);
            if ($form) $card->add($form);
        }
    }
}
