<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Message_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['componentName'] = trlKwfStatic('Message');
        return $ret;
    }

    public function getMailVars(Kwc_Shop_Cart_Order $order)
    {
        $ret = parent::getMailVars($order);
        $ret['order'] = $order;
        return $ret;
    }

}
