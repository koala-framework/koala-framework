<?php
class Kwc_Shop_Products_Detail_Form extends Kwf_Form
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

        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('Data')));
        $this->_fieldSet = $fs;
        $fs->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')));

        $generators = Kwc_Abstract::getSetting($this->getClass(), 'generators');
        if (count($generators['addToCart']['component']) > 1) {
            $this->_cards = $fs->add(new Kwf_Form_Container_Cards('component', trlKwf('Type')));
            $this->_cards->setAllowBlank(false);
            foreach ($generators['addToCart']['component'] as $component=>$class) {
                if (is_instance_of($class, 'Kwc_Shop_AddToCartAbstract_Component')) {
                    $card = $this->_cards->add();
                    $card->setName($component);
                    $card->setTitle(Kwc_Abstract::getSetting($class, 'productTypeText'));

                    $form = Kwc_Abstract_Form::createComponentForm($class);
                    if ($form) {
                        $form->setIdTemplate('{0}');
                        $card->add($form);
                    }
                }
            }
        }

        $fs->add(new Kwf_Form_Field_Checkbox('visible', trlKwf('Visible')));
        $fs->add(Kwc_Abstract_Form::createComponentForm('shopProducts_{0}-image'));

        $mf = $this->add(new Kwf_Form_Field_MultiFields('Prices'));
        $mf->setModel(Kwf_Model_Abstract::getInstance('Kwc_Shop_ProductPrices'));
        $mf->setPosition(false);
        $fs = $mf->fields->add(new Kwf_Form_Container_FieldSet(trlKwf('Price')));
            $fs->add(new Kwf_Form_Field_NumberField('price', trlKwf('Price')))
                ->setAllowBlank(false);
            $fs->add(new Kwf_Form_Field_DateTimeField('valid_from', trlKwf('Valid From')))
                ->setAllowBlank(false);

        $this->add(Kwc_Abstract_Form::createComponentForm('shopProducts_{0}-text'));
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
        $generators = Kwc_Abstract::getSetting($this->getClass(), 'generators');
        if (count($generators['addToCart']['component']) == 1 && $row->getModel()->hasColumn('component')) {
            reset($generators['addToCart']['component']);
            $row->component = key($generators['addToCart']['component']);
        }
    }
}
