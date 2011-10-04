<?php
class Vpc_Shop_Products_Detail_Form extends Vps_Form
{
    private $_cards;
    private $_fieldSet;

    public function __construct($name, $class)
    {
        $this->setClass($class);
        parent::__construct($name);
    }

    protected function _init()
    {
        parent::_init();

        $fs = $this->add(new Vps_Form_Container_FieldSet(trlVps('Data')));
        $this->_fieldSet = $fs;
        $fs->add(new Vps_Form_Field_TextField('title', trlVps('Title')));

        $generators = Vpc_Abstract::getSetting($this->getClass(), 'generators');
        if (count($generators['addToCart']['component']) > 1) {
            $this->_cards = $fs->add(new Vps_Form_Container_Cards('component', trlVps('Type')));
            $this->_cards->setAllowBlank(false);
            foreach ($generators['addToCart']['component'] as $component=>$class) {
                if (is_instance_of($class, 'Vpc_Shop_AddToCartAbstract_Component')) {
                    $card = $this->_cards->add();
                    $card->setName($component);
                    $card->setTitle(Vpc_Abstract::getSetting($class, 'productTypeText'));

                    $form = Vpc_Abstract_Form::createComponentForm($class);
                    if ($form) {
                        $form->setIdTemplate('{0}');
                        $card->add($form);
                    }
                }
            }
        }

        $fs->add(new Vps_Form_Field_Checkbox('visible', trlVps('Visible')));
        $fs->add(Vpc_Abstract_Form::createComponentForm('shopProducts_{0}-image'));

        $mf = $this->add(new Vps_Form_Field_MultiFields('Prices'));
        $mf->setModel(Vps_Model_Abstract::getInstance('Vpc_Shop_ProductPrices'));
        $mf->setPosition(false);
        $fs = $mf->fields->add(new Vps_Form_Container_FieldSet(trlVps('Price')));
            $fs->add(new Vps_Form_Field_NumberField('price', trlVps('Price')))
                ->setAllowBlank(false);
            $fs->add(new Vps_Form_Field_DateTimeField('valid_from', trlVps('Valid From')))
                ->setAllowBlank(false);

        $this->add(Vpc_Abstract_Form::createComponentForm('shopProducts_{0}-text'));
    }

    public function setModel($model)
    {
        parent::setModel($model);
        if ($this->_cards) {
            foreach ($this->_cards as $c) {
                $c->getIterator()->current()->setModel($model);
            }
        }
    }

    protected function _getFieldSet()
    {
        return $this->_fieldSet;
    }

    protected function _beforeInsert($row)
    {
        parent::_beforeInsert($row);
        $generators = Vpc_Abstract::getSetting($this->getClass(), 'generators');
        if (count($generators['addToCart']['component']) == 1) {
            reset($generators['addToCart']['component']);
            $row->component = key($generators['addToCart']['component']);
        }
    }
}
