<?php
class Kwc_Shop_Cart_Orders extends Kwf_Model_Db
{
    protected $_table = 'kwc_shop_orders';
    protected $_rowClass = 'Kwc_Shop_Cart_Order';
    protected $_siblingModels = array('Kwc_Shop_Cart_Checkout_Model');
    protected $_dependentModels = array('Products'=>'Kwc_Shop_Cart_OrderProducts');
    private static $_cartOrderId; //order-id falls sie in der session schon ge-resetted wurde
    
    protected $_cartComponentClass;

    protected function _init()
    {
        parent::_init();
        $this->_exprs['order_number'] = new Kwf_Model_Select_Expr_SumFields(
            array('number', 11000)
        );
        $this->_exprs['customer_number'] = new Kwf_Model_Select_Expr_SumFields(
            array('number', 1100)
        );
    }

    public function getCartComponentClass()
    {
        if ($this->_cartComponentClass) return $this->_cartComponentClass;
        $cacheId = 'kwc-shop-cart-default-cc';
        if (!$ret = Kwf_Cache_SimpleStatic::fetch($cacheId)) {
            $classes = array();
            foreach (Kwc_Abstract::getComponentClasses() as $c) {
                if (is_instance_of($c, 'Kwc_Shop_Cart_Component')) {
                    $classes[] = $c;
                }
            }
            if (count($classes) != 1) {
                throw new Kwf_Exception("Not exactly one Kwc_Shop_Cart_Component found, set _cartComponentClass manually");
            }
            $ret = $classes[0];
            Kwf_Cache_SimpleStatic::add($cacheId, $ret);
            $this->_cartComponentClass = $ret;
        }
        return $ret;
    }

    public final function getShopCartPlugins()
    {
        if (!isset($this->_chartPlugins)) {
            $this->_chartPlugins = array();
            $plugins = Kwc_Abstract::getSetting($this->getCartComponentClass(), 'plugins');
            foreach ($plugins as $plugin) {
                if (is_instance_of($plugin, 'Kwc_Shop_Cart_Plugins_Interface')) {
                    $this->_chartPlugins[] = new $plugin();
                }
            }
        }
        return $this->_chartPlugins;
    }

    public function getCartOrderAndSave()
    {
        $ret = $this->getCartOrder();
        if (!$ret->status) {
            $ret->status = 'cart';
            $ret->save();
            $session = new Kwf_Session_Namespace('kwcShopCart');
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
        $session = new Kwf_Session_Namespace('kwcShopCart');
        return $session->orderId;
    }

    public static function setCartOrderId($cartOrderId)
    {
        self::$_cartOrderId = $cartOrderId;
        $session = new Kwf_Session_Namespace('kwcShopCart');
        $session->orderId = $cartOrderId;
    }

    public static function setOverriddenCartOrderId($id)
    {
        self::$_cartOrderId = $id;
    }

    public static function resetCartOrderId()
    {
        $session = new Kwf_Session_Namespace('kwcShopCart');
        
        //merken damit wir noch auf die order zugreifen können
        if (!isset(self::$_cartOrderId)) {
            self::$_cartOrderId = $session->orderId;
        }

        $session->orderId = null;
    }

    public function createRow(array $data = array())
    {
        $row = parent::createRow($data);
        $row->ip = $_SERVER['REMOTE_ADDR'];
        return $row;
    }
}
