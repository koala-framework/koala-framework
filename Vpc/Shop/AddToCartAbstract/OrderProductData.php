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


    public function getAdditionalOrderData($row)
    {
        return array();
    }

    abstract public function getPrice($orderProduct);
    abstract public function getAmount($orderProduct);
    abstract public function getProductText($orderProduct);

    public function orderConfirmed($orderProduct)
    {
    }

    public function alterBackendOrderForm(Vpc_Shop_AddToCartAbstract_FrontendForm $form)
    {
    }
}
