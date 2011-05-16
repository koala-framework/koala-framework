<?php
class Vpc_Shop_Products_Detail_Form extends Vps_Form
{
    public function __construct($name, $class)
    {
        $this->setClass($class);
        parent::__construct($name);
    }

    protected function _init()
    {
        parent::_init();

        $fs = $this->add(new Vps_Form_Container_FieldSet('Daten'));
        $fs->add(new Vps_Form_Field_TextField('title', trlVps('Title')));
        $generators = Vpc_Abstract::getSetting($this->getClass(), 'generators');
        $types = array();
        foreach ($generators['addToCart']['component'] as $component => $class) {
            $types[$component] = Vpc_Abstract::getSetting($class, 'productTypeText');
        }
        if (count($types) == 1) {
            $fs->add(new Vps_Form_Field_Hidden('component'))
                ->setDefaultValue(key($types));
        } else {
            $fs->add(new Vps_Form_Field_Select('component', trlVps('Type')))
                ->setValues($types)
                ->setAllowBlank(false);
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
}
