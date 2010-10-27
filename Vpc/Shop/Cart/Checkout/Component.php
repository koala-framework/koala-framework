<?php
class Vpc_Shop_Cart_Checkout_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_Shop_Cart_Checkout_Form_Component';

        $ret['generators']['payment'] = array(
            'class' => 'Vps_Component_Generator_PseudoPage_Static',
            'component' => array(
                'prePayment' => 'Vpc_Shop_Cart_Checkout_Payment_PrePayment_Component',
                'cashOnDelivery' => 'Vpc_Shop_Cart_Checkout_Payment_CashOnDelivery_Component',
                'payPal' => 'Vpc_Shop_Cart_Checkout_Payment_PayPal_Component',
            )
        );
        $ret['cssClass'] = 'webForm webStandard';
        $ret['placeholder']['backToCart'] = trlVps('Back to cart');

        $ret['shipping'] = 0;

        $ret['flags']['hasResources'] = true;

        $ret['assetsAdmin']['dep'][] = 'ExtFormDateField';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Shop/Cart/Checkout/OrdersPanel.js';

        return $ret;
    }

    private function _getOrder()
    {
        return Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')
                            ->getCartOrder();
    }

    private function _getPaymentComponent($order)
    {
        if (!$order->payment) return null;
        $c = $this->getData()->getChildComponent('-'.$order->payment);
        if (!$c) return null;
        return $c->getComponent();
    }

    public function getShipping($order)
    {
        return $this->_getSetting('shipping');
    }

    public function getTotal($order)
    {
        $ret = $order->getSubTotal();
        $ret += $this->getShipping($order);
        $ret += $this->_getAdditionalSum($order);
        return $ret;
    }

    //kann überschrieben werden um zeilen für alle payments zu ändern
    protected function _getAdditionalSumRows($order)
    {
        $ret = array();
        if ($c = $this->_getPaymentComponent($order)) {
            $ret = array_merge($ret, $c->getAdditionalSumRows($order));
        }
        return $ret;
    }
 
    private final function _getAdditionalSum($order)
    {
        $ret = 0;
        foreach ($this->_getAdditionalSumRows($order) as $r) {
            $ret += $r['amount'];
        }
        return $ret;
    }

    //kann überschrieben werden um zeilen für alle payments zu ändern
    public function getSumRows($order)
    {
        $ret = array();
        $ret[] = array(
            'class' => 'subtotal',
            'text' => trlVps('Subtotal').':',
            'amount' => $order->getSubTotal()
        );
        $ret[] = array(
            'text' => trlVps('Shipping and Handling').':',
            'amount' => $this->getShipping($order)
        );
        $ret = array_merge($ret, $this->_getAdditionalSumRows($order));
        $ret[] = array(
            'class' => 'totalAmount',
            'text' => trlVps('Total Amount').':',
            'amount' => $this->getTotal($order)
        );
        return $ret;
    }
}
