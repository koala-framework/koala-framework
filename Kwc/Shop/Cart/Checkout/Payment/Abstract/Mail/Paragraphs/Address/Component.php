<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Address_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['componentName'] = trlKwfStatic('Address Header');
        $ret['flags']['hasMailVars'] = true;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        if ($renderer && $renderer instanceof Kwf_Component_Renderer_Mail) {
            $ret['order'] = $renderer->getRecipient();
        }
        return $ret;
    }
}
