<?php
class Kwc_Shop_Cart_Checkout_Payment_Wirecard_ConfirmLink_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['wirecardButton'] = $this->_getWirecardButton();
        $ret['options'] = array(
            'controllerUrl' =>
                Kwc_Admin::getInstance($this->getData()->componentClass)->getControllerUrl() .
                '/json-confirm-order',
            'params' => array(
                'paymentComponentId' => $this->getData()->parent->componentId
            )
        );
        return $ret;
    }

    //used in trl
    public static function buildWirecardButtonHtml($params, $payment, $order)
    {
        $wirecardCustomerId = $payment->getBaseProperty('wirecard.customerId');
        $wirecardSecret = $payment->getBaseProperty('wirecard.secret');
        if (!$wirecardCustomerId || !$wirecardSecret) {
            throw new Kwf_Exception('Set wirecard settings (customerId & secret) in config!');
        }

        $params = array_merge($params, array(
            'secret' => $wirecardSecret,
            'customerId' => $wirecardCustomerId,
            'language' => $payment->getLanguage(),
            'orderDescription' => $order->firstname . ' ' . $order->lastname . ' (' . $order->zip . '), '.$payment->trlKwf('Order: {0}', $order->number),
            'displayText' => $payment->trlKwf('Thank you very much for your order.'),
            'successURL' => $payment->getChildComponent('_success')->getAbsoluteUrl(),
            'confirmURL' => $payment->getChildComponent('_ipn')->getAbsoluteUrl(),
            'serviceURL' => $payment->getSubroot()->getAbsoluteUrl(),
            'failureURL' => $payment->getChildComponent('_failure')->getAbsoluteUrl(),
            'cancelURL' => $payment->getChildComponent('_cancel')->getAbsoluteUrl(),
            'requestFingerprintOrder' => '',
        ));
        if ($shopId = $payment->getBaseProperty('wirecard.shopId')) $params['shopId'] = $shopId;

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

        $initURL = "https://api.qenta.com/page/init.php";
        $ret = "<form action=\"$initURL\" method=\"post\" name=\"form\">\n";
        foreach ($params as $k=>$i) {
        if ($k == 'secret') continue;
            $ret .= "<input type=\"hidden\" name=\"$k\" value=\"".Kwf_Util_HtmlSpecialChars::filter($i)."\">\n";
        }

        $ret .= "<input type=\"button\" value=\"{$payment->trlKwf('Buy now')}\" class=\"submit\">\n";
        $ret .= "</form>\n";
        return $ret;

    }

    protected function _getWirecardButton()
    {
        $order = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting(
            $this->getData()->getParentByClass('Kwc_Shop_Cart_Component')->componentClass, 'childModel'
        ))->getReferencedModel('Order')->getCartOrder();
        $payment = $this->getData()->getParentByClass('Kwc_Shop_Cart_Checkout_Payment_Wirecard_Component');
        return self::buildWirecardButtonHtml($this->_getWirecardParams($payment, $order), $payment, $order);
    }

    protected function _getWirecardParams($payment, $order)
    {
        $total = $this->getData()->getParentByClass('Kwc_Shop_Cart_Checkout_Component')
            ->getComponent()->getTotal($order);
        $custom = Kwc_Shop_Cart_Checkout_Payment_Wirecard_LogModel::getEncodedCallback(
            $payment->componentId, array('orderId' => $order->id)
        );
        $params = array(
            'amount' => round($total, 2),
            'currency' => 'EUR',
            'paymentType' => Kwc_Abstract::getSetting($payment->componentClass, 'paymentType'),
            'custom' => $custom
        );
        return $params;
    }
}
