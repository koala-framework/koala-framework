<?php
class Kwc_Shop_Cart_Checkout_Payment_PayPal_ConfirmLink_Component extends Kwc_Abstract
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
        $ret['paypalButton'] = $this->_getPaypalButton();
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
    public static function buildPayPalButtonHtml($params, $payment, $order)
    {
        $paypalId =  $payment->getBaseProperty('paypalId');

        $alternative = $order->alternative_shipping_address;
        $params = array(
            'charset' => 'utf-8',
            'cmd' => '_xclick',
            'business' => $paypalId,
//            'lc' => 'AT',
            'item_name' => $payment->getComponent()->getItemName($order),
            //'cbt' => trlKwf('back to ...'), //eigener zurück zu Text könnte so gesetzt werden
            'amount' => $params['amount'],
            'currency_code' => $params['currency_code'],
            'button_subtype' => 'products',
            'no_note' => '1',
            'no_shipping' => $params['no_shipping'],
            'rm' => '1',
            'return' => $payment->getChildComponent('_confirm')->getAbsoluteUrl() . '?custom=' . urlencode($params['custom']),
            'cancel_return' => $payment->getChildComponent('_cancel')->getAbsoluteUrl(),
            'notify_url' => $payment->getChildComponent('_ipn')->getAbsoluteUrl(),
            'bn' => 'PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted',
            'custom' => $params['custom']
        );
        if ($params['no_shipping'] === 0 || $params['no_shipping'] === 2) {
            $params = array_merge($params, array(
                'cmd' => '_ext-enter',
                'redirect_cmd' => '_xclick',
                'address_override' => '1',
                'email' => $order->email,
                'first_name' => ($alternative) ? $order->shipping_firstname : $order->firstname,
                'last_name' => ($alternative) ? $order->shipping_lastname : $order->lastname,
                'address1' => ($alternative) ? $order->shipping_street : $order->street,
                'address2' => ($alternative) ? $order->shipping_addition : $order->addition,
                'city' => ($alternative) ? $order->shipping_city : $order->city,
                'country' => ($alternative) ? $order->shipping_country : $order->country,
                'zip' => ($alternative) ? $order->shipping_zip : $order->zip
            ));
        }

        $paypalDomain = Kwf_Registry::get('config')->paypalDomain;
        $ret = "<form id=\"paypalButton\" action=\"https://$paypalDomain/cgi-bin/webscr\" method=\"post\">\n";
        foreach ($params as $k=>$i) {
            $ret .= "<input type=\"hidden\" name=\"$k\" value=\"".Kwf_Util_HtmlSpecialChars::filter($i)."\">\n";
        }

        $ret .= "<input type=\"image\" src=\"https://www.paypal.com/de_DE/AT/i/btn/btn_buynowCC_LG.gif\" border=\"0\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online!\">\n";
        $ret .= "<img alt=\"\" border=\"0\" src=\"https://www.paypal.com/de_DE/i/scr/pixel.gif\" width=\"1\" height=\"1\">\n";
        $ret .= "</form>\n";
        return $ret;
    }

    protected function _getPaypalButton()
    {
        $order = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting(
            $this->getData()->getParentByClass('Kwc_Shop_Cart_Component')->componentClass, 'childModel'
        ))->getReferencedModel('Order')->getCartOrder();
        $total = $this->getData()->getParentByClass('Kwc_Shop_Cart_Checkout_Component')
            ->getComponent()->getTotal($order);

        $payment = $this->getData()->getParentByClass('Kwc_Shop_Cart_Checkout_Payment_PayPal_Component');

        $custom = Kwf_Util_PayPal_Ipn_LogModel::getEncodedCallback(
            $this->getData()->parent->componentId, array('orderId' => $order->id)
        );
        $params = array(
            'amount' => $total,
            'currency_code' => 'EUR',
            'no_shipping' => Kwc_Abstract::getSetting($payment->componentClass, 'noShipping'),
            'custom' => $custom
        );

        return self::buildPayPalButtonHtml($params, $payment, $order);
    }
}
