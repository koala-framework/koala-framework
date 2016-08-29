<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_ConfirmLink_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['confirm'] = $this->getData()->parent->getChildComponent('_confirm');
        return $ret;
    }
}
