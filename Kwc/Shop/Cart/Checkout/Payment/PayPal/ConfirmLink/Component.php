<?php
class Kwc_Shop_Cart_Checkout_Payment_PayPal_ConfirmLink_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['files'][] = 'kwf/Kwc/Shop/Cart/Checkout/Payment/PayPal/ConfirmLink/Component.js';
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['paypalButton'] = $this->_getPaypalButton();
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

    private function _getPaypalButton()
    {
        $order = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->getData()->parent->parent->parent->componentClass, 'childModel'))
            ->getReferencedModel('Order')->getCartOrder();
        $total = $this->getData()->parent->parent->getComponent()->getTotal($order);
        $paypalId =  Kwf_Registry::get('config')->paypalId;
        if (!$paypalId) {
            $paypalId = Kwc_Abstract::getSetting($this->getData()->parent->componentClass, 'business');
        }

        $custom = Kwf_Util_PayPal_Ipn_LogModel::getEncodedCallback(
            $this->getData()->parent->componentId, array('orderId' => $order->id)
        );

        $alternetive = $order->alternative_shipping_address;
        $params = array(
            'charset' => 'utf-8',
            'cmd' => '_ext-enter',
            'redirect_cmd' => '_xclick',
            'business' => $paypalId,
            'lc' => 'AT',
            'item_name' => $this->getData()->parent->getComponent()->getItemName($order),
            //'cbt' => trlKwf('back to ...'), //eigener zurück zu Text könnte so gesetzt werden
            'amount' => $total,
            'currency_code' => 'EUR',
            'button_subtype' => 'products',
            'no_note' => '1',
            'no_shipping' => '1',
            'rm' => '1',
            'return' => $this->getData()->parent->getChildComponent('_confirm')->getAbsoluteUrl() .
                '?custom=' . urlencode($custom),
            'cancel_return' => $this->getData()->parent->getChildComponent('_cancel')->getAbsoluteUrl(),
            'notify_url' => str_replace('.vivid/', '.fb-dev.vivid-planet.com/',
                $this->getData()->parent->getChildComponent('_ipn')->getAbsoluteUrl()),
            'bn' => 'PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted',
            'custom' => $custom,
            'email' => $order->email,
            'first_name' => ($alternetive) ? $order->shipping_firstname : $order->firstname,
            'last_name' => ($alternetive) ? $order->shipping_lastname : $order->lastname,
            'address1' => ($alternetive) ? $order->shipping_street : $order->street,
            'address2' => ($alternetive) ? $order->shipping_addition : $order->addition,
            'city' => ($alternetive) ? $order->shipping_city : $order->city,
            'state' => ($alternetive) ? $order->shipping_country : $order->country,
            'zip' => ($alternetive) ? $order->shipping_zip : $order->zip
        );

        $paypalDomain = Kwf_Registry::get('config')->paypalDomain;
        $ret = "<form id=\"paypalButton\" action=\"https://$paypalDomain/cgi-bin/webscr\" method=\"post\">\n";
        foreach ($params as $k=>$i) {
            $ret .= "<input type=\"hidden\" name=\"$k\" value=\"".htmlspecialchars($i)."\">\n";
        }

        $ret .= "<input type=\"image\" src=\"https://www.paypal.com/de_DE/AT/i/btn/btn_buynowCC_LG.gif\" border=\"0\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online!\">\n";
        $ret .= "<img alt=\"\" border=\"0\" src=\"https://www.paypal.com/de_DE/i/scr/pixel.gif\" width=\"1\" height=\"1\">\n";
        $ret .= "</form>\n";
        return $ret;
    }
}
