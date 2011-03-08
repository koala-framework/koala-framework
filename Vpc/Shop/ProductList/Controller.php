<?php
class Vpc_Shop_ProductList_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    public function _initFields()
    {
        parent::_initFields();

        $productTypes = array();
        $productsModel = array();
        foreach (Vpc_Abstract::getComponentClasses() as $class) {
            if (is_instance_of($class, 'Vpc_Shop_Products_Detail_Component')) {
                $generators = Vpc_Abstract::getSetting($class, 'generators');
                foreach ($generators['addToCart']['component'] as $key => $c) {
                    $productTypes[$key] = Vpc_Abstract::getSetting($c, 'productTypeText');
                }
            }
            if (is_instance_of($class, 'Vpc_Shop_Products_Directory_Component')) {
                $productsModel = Vps_Model_Abstract::getInstance(
                    Vpc_Abstract::getSetting($class, 'childModel')
                );
            }
        }

        $cards = $this->_form->add(new Vps_Form_Container_Cards('component', trlVps('Type')));
        $cards->getCombobox()
            ->setShowNoSelection(true)
            ->setEmptyText(trlVps('All'));
        foreach ($productTypes as $key => $title) {
            $card = $cards->add();
            $card->setName($key);
            $card->setTitle($title);
            $select = $productsModel->select()
                ->whereEquals('component', $key)
                ->order('pos');
            $card->add(new Vps_Form_Field_Select('product_' . $key, trlVps('Product')))
                ->setValues($productsModel->getRows($select))
                ->setShowNoSelection(true)
                ->setEmptyText(trlVps('All'));
        }
    }
}
