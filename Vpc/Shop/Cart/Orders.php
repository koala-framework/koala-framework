<?php
class Vpc_Shop_Cart_Orders extends Vps_Model_Db
{
    protected $_table = 'vpc_shop_orders';
    protected $_rowClass = 'Vpc_Shop_Cart_Order';
    protected $_siblingModels = array('Vpc_Shop_Cart_Checkout_Model');
    protected $_dependentModels = array('Products'=>'Vpc_Shop_Cart_OrderProducts');

    public function getCartOrderAndSave()
    {
        $ret = $this->getCartOrder();
        if (!$ret->status) {
            $ret->status = 'cart';
            $ret->save();
            $session = new Zend_Session_Namespace('vpcShopCart');
            $session->orderId = $ret->id;
        }
        return $ret;
    }

    public function getCartOrder()
    {
        $ret = null;
        $session = new Zend_Session_Namespace('vpcShopCart');
        if ($session->orderId) {
            $ret = $this->find($session->orderId)->current();
        }
        if (!$ret) {
            $ret = $this->createRow();
        }
        return $ret;
    }

    public static function getCartOrderId()
    {
        $session = new Zend_Session_Namespace('vpcShopCart');
        return $session->orderId;
    }

    public static function resetCartOrderId()
    {
        $session = new Zend_Session_Namespace('vpcShopCart');
        $session->orderId = null;
    }

    public function createRow(array $data = array())
    {
        $row = parent::createRow($data);
        $row->ip = $_SERVER['REMOTE_ADDR'];
        return $row;
    }
}
