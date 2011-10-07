<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_OrderData
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
            $c = Kwc_Abstract::getSetting($componentClass, 'orderData');
            $instances[$componentClass] = new $c($componentClass);
        }
        return $instances[$componentClass];
    }

    //da kann zB eine Nachnahmegebühr zurückgegeben werden
    //darf nur von Kwc_Shop_Cart_OrderData::getAdditionalSumRows() aufgerufen werden!
    public function getAdditionalSumRows(Kwc_Shop_Cart_Order $order)
    {
        return array();
    }
}
