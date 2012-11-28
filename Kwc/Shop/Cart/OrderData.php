<?php
class Kwc_Shop_Cart_OrderData
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
            $c = Kwc_Abstract::getSetting($componentClass, 'orderData');
            $instances[$componentClass] = new $c($componentClass);
        }
        return $instances[$componentClass];
    }

    public final function getShopCartPlugins()
    {
        if (!isset($this->_chartPlugins)) {
            $this->_chartPlugins = array();
            $plugins = Kwc_Abstract::getSetting($this->_class, 'plugins');
            foreach ($plugins as $plugin) {
                if (is_instance_of($plugin, 'Kwc_Shop_Cart_Plugins_Interface')) {
                    $this->_chartPlugins[] = new $plugin();
                }
            }
        }
        return $this->_chartPlugins;
    }

    //kann überschrieben werden um shipping zB abhängig von bestellmenge zu machen
    protected function _getShipping($order)
    {
        return Kwc_Abstract::getSetting(
            Kwc_Abstract::getChildComponentClass($this->_class, 'checkout'),
            'shipping'
        );
    }

    //return false to completely hide shipping
    protected function _hasShipping($order)
    {
        return true;
    }

    public final function getTotal($order)
    {
        $ret = $order->getSubTotal();
        if ($this->_hasShipping($order)) {
            $ret += $this->_getShipping($order);
        }
        foreach ($this->_getAdditionalSumRows($order, $ret) as $r) {
            $ret += $r['amount'];
        }
        return $ret;
    }

    //kann überschrieben werden um zeilen für alle payments zu ändern
    protected function _getAdditionalSumRows($order, $total)
    {
        $ret = array();
        if ($order instanceof Kwc_Shop_Cart_Order) {
            $payments = Kwc_Abstract::getChildComponentClasses(
                Kwc_Abstract::getChildComponentClass($this->_class, 'checkout'), 'payment');
            if (isset($payments[$order->payment])) {
                $rows = Kwc_Shop_Cart_Checkout_Payment_Abstract_OrderData
                    ::getInstance($payments[$order->payment])
                    ->getAdditionalSumRows($order);
                foreach ($rows as $r) $total += $r['amount'];
                $ret = array_merge($ret, $rows);
            }
        }
        foreach ($this->getShopCartPlugins() as $p) {
            $rows = $p->getAdditionalSumRows($order, $total);
            foreach ($rows as $r) $total += $r['amount'];
            $ret = array_merge($ret, $rows);
        }
        return $ret;
    }

    public final function getSumRows($order)
    {
        $ret = array();
        $subTotal = $order->getSubTotal();
        $ret[] = array(
            'class' => 'valueOfGoods',
            'text' => trlKwf('value of goods').':',
            'amount' => $subTotal
        );
        if (Kwc_Abstract::getSetting($this->_class, 'vatRate')) {
            $vat = 1+Kwc_Abstract::getSetting($this->_class, 'vatRate');
            $ret[] = array(
                'text' => trlKwf('net amount').':',
                'amount' => round($subTotal/$vat, 2)
            );
            $ret[] = array(
                'text' => trlKwf('+{0}% VAT', ($vat-1 )*100).':',
                'amount' => round($subTotal - $subTotal/$vat, 2)
            );
        }
        $shipping = 0;
        if ($this->_hasShipping($order)) {
            $shipping = $this->_getShipping($order);
            $vat = 1+Kwc_Abstract::getSetting($this->_class, 'vatRateShipping');
            $ret[] = array(
                'class' => 'shippingHandling',
                'text' => trlKwf('Shipping and Handling').':',
                'amount' => round($shipping/$vat, 2)
            );
            if ($shipping) {
                $ret[] = array(
                    'text' => trlKwf('+{0}% VAT', ($vat-1 )*100).':',
                    'amount' => round($shipping - $shipping/$vat, 2)
                );
            }
        }
        $ret = array_merge($ret, $this->_getAdditionalSumRows($order, $subTotal+$shipping));
        $ret[] = array(
            'class' => 'totalAmount',
            'text' => trlKwf('Total Amount').':',
            'amount' => $this->getTotal($order)
        );
        return $ret;
    }
}
