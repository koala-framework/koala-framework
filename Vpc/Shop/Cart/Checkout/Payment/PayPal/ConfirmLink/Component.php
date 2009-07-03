<?php
class Vpc_Shop_Cart_Checkout_Payment_PayPal_ConfirmLink_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['paypalButton'] = $this->_getPaypalButton();
        $ret['viewCache'] = false;
        return $ret;
    }

    private function _getPaypalButton()
    {
        $order = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')->getCartOrder();

        $params = array(
            'cmd' => '_xclick',
            'business' => 'N5CLQYARCKGVE',
            'lc' => 'AT',
            'item_name' => 'Bestellung über Babytuch.com',
            'amount' => '9.90',
            'currency_code' => 'EUR',
            'button_subtype' => 'products',
            'cn' => 'Mitteilung an den Händler',
            'no_shipping' => '2',
            'rm' => '1',
            'return' => $this->getData()->parent->getChildComponent('_confirm')->url,
            'cancel_return' => $this->getData()->parent->parent->parent->url,
            'bn' => 'PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted',
            'custom' => Vps_Util_PayPal_Ipn_LogModel::getEncodedCallback(
                            $this->getData()->parent->componentClass,
                            'processIpn',
                            array(
                                'orderId' => $order->id
                            )),
        );
        $ret = "<form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\">\n";
        foreach ($params as $k=>$i) {
            $ret .= "<input type=\"hidden\" name=\"$k\" value=\"".htmlspecialchars($i)."\">\n";
        }
        $ret .= "<input type=\"image\" src=\"https://www.paypal.com/de_DE/AT/i/btn/btn_buynowCC_LG.gif\" border=\"0\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online!\">\n";
        $ret .= "<img alt=\"\" border=\"0\" src=\"https://www.paypal.com/de_DE/i/scr/pixel.gif\" width=\"1\" height=\"1\">\n";
        $ret .= "</form>\n";
        return $ret;
    }
}
