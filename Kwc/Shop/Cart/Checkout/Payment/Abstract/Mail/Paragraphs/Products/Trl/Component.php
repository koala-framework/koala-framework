<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Products_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        if ($renderer && $renderer instanceof Kwf_Component_Renderer_Mail) {
            $order = $renderer->getRecipient();
            $ret['items'] = $order->getProductsDataWithProduct($this->getData());
        }
        return $ret;
    }

}

