<?php
class Vpc_Shop_Cart_Checkout_Payment_PayPal_ConfirmLink_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['paypalButton'] = $this->_getPaypalButton();
        return $ret;
    }

    private function _getPaypalButton()
    {
        $order = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')->getCartOrder();
        $total = $this->getData()->parent->parent->getComponent()->getTotal($order);
        $domain = 'http://'.Vps_Registry::get('config')->server->domain;
        $params = array(
            'charset' => 'utf-8',
            'cmd' => '_xclick',
            'business' => Vpc_Abstract::getSetting($this->getData()->parent->componentClass, 'business'),
            'lc' => 'AT',
            'item_name' => $this->getData()->parent->getComponent()->getItemName($order),
            //'cbt' => trlVps('back to ...'), //eigener zurück zu Text könnte so gesetzt werden
            'amount' => $total,
            'currency_code' => 'EUR',
            'button_subtype' => 'products',
            'no_note' => '1',
            'no_shipping' => '1',
            'rm' => '1',
            'return' => $domain.$this->getData()->parent->getChildComponent('_confirm')->url,
            'cancel_return' => $domain.$this->getData()->parent->parent->parent->url,
            'notify_url' => $domain.'/paypal_ipn',
            'bn' => 'PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted',
            'custom' => Vps_Util_PayPal_Ipn_LogModel::getEncodedCallback(
                            $this->getData()->parent->componentId,
                            array(
                                'orderId' => $order->id
                            )),
        );

        $paypalDomain = Vps_Registry::get('config')->paypalDomain;
        $ret = "<form action=\"https://$paypalDomain/cgi-bin/webscr\" method=\"post\">\n";
        foreach ($params as $k=>$i) {
            $ret .= "<input type=\"hidden\" name=\"$k\" value=\"".htmlspecialchars($i)."\">\n";
        }

        $ret .= "<input type=\"image\" src=\"https://www.paypal.com/de_DE/AT/i/btn/btn_buynowCC_LG.gif\" border=\"0\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online!\">\n";
        $ret .= "<img alt=\"\" border=\"0\" src=\"https://www.paypal.com/de_DE/i/scr/pixel.gif\" width=\"1\" height=\"1\">\n";
        $ret .= "</form>\n";
        return $ret;
    }
}
