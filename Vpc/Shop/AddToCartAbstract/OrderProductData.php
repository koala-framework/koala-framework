<?php
abstract class Vpc_Shop_AddToCartAbstract_OrderProductData
{
    protected $_class;

    public function __construct($componentClass)
    {
        $this->_class = $componentClass;
    }

    /**
     * @return $this
     */
    public static function getInstance($componentClass)
    {
        static $instances = array();
        if (!isset($instances[$componentClass])) {
            $c = Vpc_Abstract::getSetting($componentClass, 'orderProductData');
            $instances[$componentClass] = new $c($componentClass);
        }
        return $instances[$componentClass];
    }


    public function getAdditionalOrderData(Vpc_Shop_Cart_OrderProduct $row)
    {
        return array();
    }

    abstract public function getPrice(Vpc_Shop_Cart_OrderProduct $orderProduct);
    abstract public function getAmount(Vpc_Shop_Cart_OrderProduct $orderProduct);
    abstract public function getProductText(Vpc_Shop_Cart_OrderProduct $orderProduct);

    public function orderConfirmed(Vpc_Shop_Cart_OrderProduct $orderProduct)
    {
    }

    public function alterBackendOrderForm(Vps_Form $form)
    {
    }
}
