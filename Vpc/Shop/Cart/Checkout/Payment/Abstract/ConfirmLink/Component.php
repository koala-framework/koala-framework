<?php
class Vpc_Shop_Cart_Checkout_Payment_Abstract_ConfirmLink_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['link'] = $this->getData()->parent->getChildComponent('_confirm');
        return $ret;
    }
}
