<?php
class Vpc_Shop_Cart_Checkout_OrderProductController extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array();
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Vpc_Shop_Cart_OrderProducts';

    protected function _initFields()
    {
        parent::_initFields();
        $m = Vps_Model_Abstract::getInstance('Vpc_Shop_Products');
        $s = $m->select();
        $s->whereEquals('visible', 1);
        $s->order('pos');
        $data = array();
        foreach ($m->getRows($s) as $product) {
            $data[] = array(
                $product->current_price_id,
                $product->__toString().' ('.$product->current_price.' €)'
            );
        }
        $this->_form->add(new Vps_Form_Field_Select('shop_product_price_id', trlVps('Product')))
            ->setValues($data);

        //keine optimale l�sung, theoretisch k�nnen mehrere unterschiedliche
        //addtocart komponenten verwendet werden
        foreach (Vpc_Abstract::getComponentClasses() as $c) {
            if (is_instance_of($c, 'Vpc_Shop_AddToCart_Component')) {
                break;
            }
        }
        $this->_form->add(Vpc_Abstract_Form::createComponentForm($c, 'add'))
            ->setIdTemplate('{0}');
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->shop_order_id = $this->_getParam('shop_order_id');
        $productId = $row->getParentRow('ProductPrice')->shop_product_id;
        $addToCart = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId('shopProducts_'.$productId.'-addToCart');
        $row->add_component_id = $addToCart->dbId;
    }
}
