<?php
class Vpc_Abstract_Cards_Form extends Vpc_Abstract_Form
{
    protected function _init()
    {
        parent::_init();

        $gen = Vpc_Abstract::getSetting($this->getClass(), 'generators');
        $classes = $gen['child']['component'];
        $cards = $this->add(new Vps_Form_Container_Cards('component', trlVps('Type')))
            ->setDefaultValue(key($classes));
        $cards->getCombobox()
            ->setWidth(250)
            ->setListWidth(250);

        foreach ($classes as $name => $class) {
            $forms = array();
            $admin = Vpc_Admin::getInstance($class);
            $forms = $admin->getCardForms();
            if (!$forms) {
                //wenns gar keine forms gibt
                $card = $cards->add();
                $card->setTitle(Vpc_Abstract::getSetting($class, 'componentName'));
                $card->setName($name);
            }
            foreach ($forms as $k=>$i) {
                $form = $i['form'];
                if ($form) {
                    if (!$form->getIdTemplate()) {
                        $form->setIdTemplate('{0}-child');
                    }
                    $form->setAutoHeight(true);
                    $form->setBaseCls('x-plain');
                }

                $card = $cards->add();
                $card->setTitle($i['title']);
                if (count($forms) == 1) {
                    $card->setName($name);
                    if ($form) $form->setName($name);
                } else {
                    $card->setName($name.'_'.$k); //damits eindeutig ist wenn zB news mehrere forms hat
                    if ($form) $form->setName($name.'_'.$k);
                }
                if ($form) $card->add($form);
            }
        }
        $cards->getCombobox()->getData()->cards = $cards->fields;
    }
}