<?php
class Kwc_Shop_Cart_Checkout_Payment_Wirecard_ConfirmLink_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['assets']['files'][] = 'kwf/Kwc/Shop/Cart/Checkout/Payment/Wirecard/ConfirmLink/Component.js';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['wirecardButton'] = $this->_getWirecardButton();
        $ret['options'] = array(
            'controllerUrl' =>
                Kwc_Admin::getInstance(get_class($this))->getControllerUrl() .
                '/json-confirm-order',
            'params' => array(
                'paymentComponentId' => $this->getData()->parent->componentId
            )
        );
        return $ret;
    }

    protected function _getWirecardButton()
    {
        $order = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting(
            $this->getData()->getParentByClass('Kwc_Shop_Cart_Component')->componentClass, 'childModel'
        ))->getReferencedModel('Order')->getCartOrder();
        $total = $this->getData()->getParentByClass('Kwc_Shop_Cart_Checkout_Component')
            ->getComponent()->getTotal($order);

        $wirecardCustomerId = $this->getData()->getBaseProperty('wirecard.customerId');
        $wirecardSecret = $this->getData()->getBaseProperty('wirecard.secret');
        if (!$wirecardCustomerId || !$wirecardSecret) {
            throw new Kwf_Exception('Set wirecard settings (customerId & secret) in config!');
        }

        $custom = Kwc_Shop_Cart_Checkout_Payment_Wirecard_LogModel::getEncodedCallback(
            $this->getData()->parent->componentId, array('orderId' => $order->id)
        );

        $payment = $this->getData()->getParentByClass('Kwc_Shop_Cart_Checkout_Payment_Wirecard_Component');
        $orderDescription = $order->firstname . ' ' . $order->lastname . ' (' . $order->zip . '), Bestellung: ' . $order->id;
        $params = array(
            'secret' => $wirecardSecret,
            'customerId' => $wirecardCustomerId,
            'amount' => round($total, 2),
            'currency' => 'EUR',
            'language' => $this->getData()->getLanguage(),
            'orderDescription' => $orderDescription,
            'displayText' => $this->getData()->trlKwf('Thank you very much for your order.'),
            'successURL' => $payment->getChildComponent('_success')->getAbsoluteUrl(),
            'confirmURL' => $payment->getChildComponent('_ipn')->getAbsoluteUrl(),
            'serviceURL' => $this->getData()->getSubroot()->getAbsoluteUrl(),
            'failureURL' => $payment->getChildComponent('_failure')->getAbsoluteUrl(),
            'cancelURL' => $payment->getChildComponent('_cancel')->getAbsoluteUrl(),
            'requestFingerprintOrder' => '',
            'paymentType' => Kwc_Abstract::getSetting($payment->componentClass, 'paymentType'),
            'custom' => $custom
        );
        if ($shopId = $this->getData()->getBaseProperty('wirecard.shopId')) $params['shopId'] = $shopId;

        $requestFingerprintSeed  = "";
        $exclude = array('requestFingerprintOrder');
        foreach ($params as $key=>$value) {
            if (in_array($key, $exclude)) continue;
            $params['requestFingerprintOrder'] .= "$key,";
            $requestFingerprintSeed  .= $value;
        }
        $params['requestFingerprintOrder'] .= "requestFingerprintOrder";
        $requestFingerprintSeed  .= $params['requestFingerprintOrder'];
        $params['requestFingerprint'] = md5($requestFingerprintSeed);

        $initURL = "https://checkout.wirecard.com/page/init.php";
        $ret = "<form action=\"$initURL\" method=\"post\" name=\"form\">\n";
        foreach ($params as $k=>$i) {
            if ($k == 'secret') continue;
            $ret .= "<input type=\"hidden\" name=\"$k\" value=\"".htmlspecialchars($i)."\">\n";
        }

        $ret .= "<input type=\"button\" value=\"{$this->getData()->trlKwf('Buy now')}\" class=\"submit\">\n";
        $ret .= "</form>\n";
        return $ret;
    }
}
