<?php
class Vpc_Shop_Cart_OrderData
{
    protected $_class;
    private $_chartPlugins;

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
            $c = Vpc_Abstract::getSetting($componentClass, 'orderData');
            $instances[$componentClass] = new $c($componentClass);
        }
        return $instances[$componentClass];
    }

    public final function getShopCartPlugins()
    {
        if (!isset($this->_chartPlugins)) {
            $this->_chartPlugins = array();
            $plugins = Vpc_Abstract::getSetting($this->_class, 'plugins');
            foreach ($plugins as $plugin) {
                if (is_instance_of($plugin, 'Vpc_Shop_Cart_Plugins_Interface')) {
                    $this->_chartPlugins[] = new $plugin();
                }
            }
        }
        return $this->_chartPlugins;
    }

    //kann überschrieben werden um shipping zB abhängig von bestellmenge zu machen
    protected function _getShipping(Vpc_Shop_Cart_Order $order)
    {
        return Vpc_Abstract::getSetting(
            Vpc_Abstract::getChildComponentClass($this->_class, 'checkout'),
            'shipping'
        );
    }

    public final function getTotal(Vpc_Shop_Cart_Order $order)
    {
        $ret = $order->getSubTotal();
        $ret += $this->_getShipping($order);
        foreach ($this->_getAdditionalSumRows($order, $ret) as $r) {
            $ret += $r['amount'];
        }
        return $ret;
    }

    //kann überschrieben werden um zeilen für alle payments zu ändern
    protected function _getAdditionalSumRows(Vpc_Shop_Cart_Order $order, $total)
    {
        $ret = array();
        $payments = Vpc_Abstract::getChildComponentClasses(
            Vpc_Abstract::getChildComponentClass($this->_class, 'checkout'), 'payment');
        if (isset($payments[$order->payment])) {
            $rows = Vpc_Shop_Cart_Checkout_Payment_Abstract_OrderData
                ::getInstance($payments[$order->payment])
                ->getAdditionalSumRows($order);
            foreach ($rows as $r) $total += $r['amount'];
            $ret = array_merge($ret, $rows);
        }
        foreach ($this->getShopCartPlugins() as $p) {
            $rows = $p->getAdditionalSumRows($order, $total);
            foreach ($rows as $r) $total += $r['amount'];
            $ret = array_merge($ret, $rows);
        }
        return $ret;
    }

    public final function getSumRows(Vpc_Shop_Cart_Order $order)
    {
        $ret = array();
        $subTotal = $order->getSubTotal();
        $ret[] = array(
            'class' => 'valueOfGoods',
            'text' => trlVps('value of goods').':',
            'amount' => $subTotal
        );
        $ret[] = array(
            'text' => trlVps('net amount').':',
            'amount' => round($subTotal/1.2, 2)
        );
        $ret[] = array(
            'text' => trlVps('+20% VAT').':',
            'amount' => round($subTotal - $subTotal/1.2, 2)
        );
        $shipping = $this->_getShipping($order);
        $ret[] = array(
            'text' => trlVps('Shipping and Handling').':',
            'amount' => round($shipping/1.2, 2)
        );
        if ($shipping) {
            $ret[] = array(
                'text' => trlVps('+20% VAT').':',
                'amount' => round($shipping - $shipping/1.2, 2)
            );
        }
        $ret = array_merge($ret, $this->_getAdditionalSumRows($order, $subTotal+$shipping));
        $ret[] = array(
            'class' => 'totalAmount',
            'text' => trlVps('Total Amount').':',
            'amount' => $this->getTotal($order)
        );
        return $ret;
    }
}
