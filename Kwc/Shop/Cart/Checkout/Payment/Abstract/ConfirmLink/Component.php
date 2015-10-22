<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_ConfirmLink_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['confirm'] = trlKwfStatic('Send order');
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['confirm'] = $this->getData()->parent->getChildComponent('_confirm');
        return $ret;
    }
}
