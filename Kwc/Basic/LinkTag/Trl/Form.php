<?php
class Vpc_Basic_LinkTag_Trl_Form_ComponentData extends Vps_Data_Abstract
{
    public function load($row)
    {
        $c = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($row->id, array('ignoreVisible'=>true));
        return $c->chained->getComponent()->getRow()->component;
    }

    public function save(Vps_Model_Row_Interface $row, $data)
    {
    }
}

class Vpc_Basic_LinkTag_Trl_Form extends Vpc_Abstract_Form
{
    protected function _init()
    {
        parent::_init();
        $this->_model = new Vps_Model_FnF();

        $gen = Vpc_Abstract::getSetting($this->getClass(), 'generators');
        $classes = $gen['child']['component'];
        $cards = $this->add(new Vps_Form_Container_Cards('component', trlVps('Link type')))
            ->setDefaultValue(key($classes));

        $hidden = new Vps_Form_Field_Hidden('component');
        $hidden->setData(new Vpc_Basic_LinkTag_Trl_Form_ComponentData());
        $cards->setCombobox($hidden);

        foreach ($classes as $name => $class) {
            $form = Vpc_Abstract_Form::createChildComponentForm($this->getClass(), '-' . $name, $name);
            if ($form) {
                $form->setIdTemplate('{0}-child');
                $form->setAutoHeight(true);
                $form->setBaseCls('x-plain');
            }

            $card = $cards->add();
            $card->setName($name);
            if ($form) $card->add($form);
        }
    }
}
