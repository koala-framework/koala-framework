<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_OrderHeader_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $c = $this->getData()->parent->chained->componentClass;
        if (!is_instance_of($c, 'Kwc_Shop_Cart_Checkout_Payment_None_Component')) {
            $ret['paymentTypeText'] = $this->getData()->trlStaticExecute(
                Kwc_Abstract::getSetting($c, 'componentName')
            );
        }
        return $ret;
    }

}

