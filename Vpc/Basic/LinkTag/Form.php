<?php
class Vpc_Basic_LinkTag_Form_Data extends Vps_Data_Abstract
{
    public $cards;
    public function load($row)
    {
        foreach ($this->cards as $card) {
            $n = $card->getName();
            if (strpos($n, '_') && $row->component == substr($n, 0, strpos($n, '_'))) {
                if ($card->fields->first()->getIsCurrentLinkTag($row)) {
                    return $n;
                }
            }
        }
        return $row->component;
    }

    public function save(Vps_Model_Row_Interface $row, $data)
    {
        if (strpos($data, '_')!==false) {
            $data = substr($data, 0, strpos($data, '_'));
        }
        $row->component = $data;
    }
}

class Vpc_Basic_LinkTag_Form extends Vpc_Abstract_Form
{
    protected function _init()
    {
        parent::_init();

        $gen = Vpc_Abstract::getSetting($this->getClass(), 'generators');
        $classes = $gen['link']['component'];
        $cards = $this->add(new Vps_Form_Container_Cards('component', trlVps('Link type')))
            ->setDefaultValue(key($classes));
        $cards->getCombobox()
            ->setData(new Vpc_Basic_LinkTag_Form_Data())
            ->setWidth(250)
            ->setListWidth(250);

        foreach ($classes as $name => $class) {
            $admin = Vpc_Admin::getInstance($class);
            if ($admin instanceof Vpc_Basic_LinkTag_Abstract_Admin) {
                $forms = $admin->getLinkTagForms();
                foreach ($forms as $k=>$i) {
                    $form = $i['form'];
                    if ($form) {
                        $form->setIdTemplate('{0}-link');
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
            } else {
                $card = $cards->add();
                $card->setName($name);
                $card->setTitle(Vpc_Abstract::getSetting($class, 'componentName'));
            }
        }
        $cards->getCombobox()->getData()->cards = $cards->fields;
    }
}
