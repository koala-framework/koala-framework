<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_ConfirmLink_Component extends Kwc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['confirm'] = $this->getData()->parent->getChildComponent('_confirm');
        $ret['placeholder']['confirm'] = 'Send order';
        return $ret;
    }
}
