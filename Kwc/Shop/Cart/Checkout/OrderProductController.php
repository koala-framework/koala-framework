<?php
class Kwc_Shop_Cart_Checkout_OrderProductController extends Kwf_Controller_Action_Auto_Form
{
    protected $_buttons = array();
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Kwc_Shop_Cart_OrderProducts';

    protected function _initFields()
    {
        parent::_initFields();
        $cards = $this->_form->add(new Kwf_Form_Container_Cards('add_component_class', trlKwf('Type')));
        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            if (is_instance_of($c, 'Kwc_Shop_AddToCartAbstract_Component')) {
                $card = $cards->add();
                $card->setName($c);
                $card->setTitle(Kwc_Abstract::getSetting($c, 'productTypeText'));

                $formClass = Kwc_Admin::getComponentClass($c, 'FrontendForm');
                $form = new $formClass($c, $c);
                $form->setModel(Kwf_Model_Abstract::getInstance('Kwc_Shop_Cart_OrderProducts'));
                $form->setIdTemplate('{0}');
                Kwc_Shop_AddToCartAbstract_OrderProductData::getInstance($c)
                    ->alterBackendOrderForm($form);
                $card->add($form);
            }
        }
        $cards->setAllowBlank(false);
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->shop_order_id = $this->_getParam('shop_order_id');
    }
}
