<?php
class Kwc_Abstract_Cards_Trl_Form_ComponentData extends Kwf_Data_Abstract
{
    public function load($row)
    {
        $c = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($row->id, array('ignoreVisible'=>true));

        if ($c && is_instance_of($c->chained->componentClass, 'Kwc_Abstract_Cards_Component')) {
            return $c->chained->getComponent()->getRow()->component;
        } else {
            return '';
        }
    }

    public function save(Kwf_Model_Row_Interface $row, $data)
    {
    }
}

class Kwc_Abstract_Cards_Trl_Form extends Kwc_Abstract_Form
{
    protected function _init()
    {
        parent::_init();
        $this->_model = new Kwf_Model_FnF();

        $gen = Kwc_Abstract::getSetting($this->getClass(), 'generators');
        $classes = $gen['child']['component'];
        $cards = $this->add(new Kwf_Form_Container_Cards('component', trlKwf('Type')))
            ->setDefaultValue(key($classes));

        $hidden = new Kwf_Form_Field_Hidden('component');
        $hidden->setData(new Kwc_Abstract_Cards_Trl_Form_ComponentData());
        $cards->setCombobox($hidden);

        foreach ($classes as $name => $class) {
            $form = Kwc_Abstract_Form::createChildComponentForm($this->getClass(), '-' . $name, $name);
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
