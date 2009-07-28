<?php
class Vpc_Shop_Cart_Orders extends Vps_Model_Db
{
    protected $_table = 'vpc_shop_orders';
    protected $_rowClass = 'Vpc_Shop_Cart_Order';
    protected $_siblingModels = array('Vpc_Shop_Cart_Checkout_Model');
    protected $_dependentModels = array('Products'=>'Vpc_Shop_Cart_OrderProducts');
    private static $_cartOrderId; //order-id falls sie schon ge-resetted wurde

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
        $orderId = self::getCartOrderId();
        if ($orderId) {
            $ret = $this->find($orderId)->current();
        }
        if (!$ret) {
            $ret = $this->createRow();
        }
        return $ret;
    }

    /**
     * Gibt die cart order id zurück, auch wenn sie in diesem request 
     * schon per resetCartOrderId zurück gesetzt wurde.
     */
    public static function getCartOrderId()
    {
        if (isset(self::$_cartOrderId)) {
            return self::$_cartOrderId;
        }
        $session = new Zend_Session_Namespace('vpcShopCart');
        return $session->orderId;
    }

    public static function resetCartOrderId()
    {
        $session = new Zend_Session_Namespace('vpcShopCart');
        
        //merken damit wir noch auf die order zugreifen können
        self::$_cartOrderId = $session->orderId;

        $session->orderId = null;
    }

    public function createRow(array $data = array())
    {
        $row = parent::createRow($data);
        $row->ip = $_SERVER['REMOTE_ADDR'];
        return $row;
    }
}
