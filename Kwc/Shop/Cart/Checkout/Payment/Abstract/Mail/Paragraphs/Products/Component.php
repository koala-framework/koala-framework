<?php
//TODO: könnte von Kwc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Paragraphs_Products_Component erben
class Kwc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Products_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['componentName'] = trlKwfStatic('Order Products');
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        if ($renderer && $renderer instanceof Kwf_Component_Renderer_Mail) {
            $order = $renderer->getRecipient();
            $ret['items'] = $order->getProductsData();

            $c = $this->getData()->getParentByClass('Kwc_Shop_Cart_Checkout_Component');
            $ret['sumRows'] = $c->getComponent()->getSumRows($order);
        }
        return $ret;
    }
}
