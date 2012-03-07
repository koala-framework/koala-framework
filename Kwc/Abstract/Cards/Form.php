<?php
class Kwc_Abstract_Cards_Form_ComboBoxData extends Kwf_Data_Table
{
    private $_classes;
    public function __construct($classes)
    {
        $this->_classes = $classes;
        parent::__construct();
    }

    public function load($row)
    {
        $ret = array(
            'value' => parent::load($row),
            'visibleCardForms' => array(),
        );
        foreach ($this->_classes as $name => $class) {
            $admin = Kwc_Admin::getInstance($class);
            $forms = $admin->getCardForms();
            if (!$forms) {
                //wenns gar keine forms gibt
                $card = $cards->add();
                $card->setTitle(Kwc_Abstract::getSetting($class, 'componentName'));
                $card->setName($name);
            }
            if (count($forms) == 1) {
                foreach ($admin->getVisibleCardForms($row->component_id) as $k) {
                    $ret['visibleCardForms'][] = $name;
                }
            } else {
                foreach ($admin->getVisibleCardForms($row->component_id) as $k) {
                    $ret['visibleCardForms'][] = $name.'_'.$k;
                }
            }
        }
        return $ret;
    }
}

class Kwc_Abstract_Cards_Form extends Kwc_Abstract_Form
{
    protected function _init()
    {
        parent::_init();

        $gen = Kwc_Abstract::getSetting($this->getClass(), 'generators');
        $classes = $gen['child']['component'];
        $cards = $this->add(new Kwf_Form_Container_Cards('component', trlKwf('Type')))
            ->setDefaultValue(key($classes));
        $cards->getCombobox()
            ->setWidth(250)
            ->setListWidth(250)
            ->setXtype('kwc.abstract.cards.combobox')
            ->setData(new Kwc_Abstract_Cards_Form_ComboBoxData($classes));
        foreach ($classes as $name => $class) {
            $forms = array();
            $admin = Kwc_Admin::getInstance($class);
            $forms = $admin->getCardForms();
            if (!$forms) {
                //wenns gar keine forms gibt
                $card = $cards->add();
                $card->setTitle(Kwc_Abstract::getSetting($class, 'componentName'));
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
    }
}