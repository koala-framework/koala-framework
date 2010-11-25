<?php
class Vpc_Shop_Cart_Checkout_OrderProductController extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array();
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Vpc_Shop_Cart_OrderProducts';

    protected function _initFields()
    {
        parent::_initFields();
        $cards = $this->_form->add(new Vps_Form_Container_Cards('add_component_class', trlVps('Type')));
        foreach (Vpc_Abstract::getComponentClasses() as $c) {
            if (is_instance_of($c, 'Vpc_Shop_AddToCartAbstract_Component')) {
                $card = $cards->add();
                $card->setName($c);
                $card->setTitle(Vpc_Abstract::getSetting($c, 'productTypeText'));

                $formClass = Vpc_Admin::getComponentClass($c, 'FrontendForm');
                $form = new $formClass($c, $c);
                $form->setModel(Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_OrderProducts'));
                $form->setIdTemplate('{0}');
                Vpc_Shop_AddToCartAbstract_OrderProductData::getInstance($c)
                    ->alterBackendOrderForm($form);
                $card->add($form);
            }
        }
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->shop_order_id = $this->_getParam('shop_order_id');
    }
}
