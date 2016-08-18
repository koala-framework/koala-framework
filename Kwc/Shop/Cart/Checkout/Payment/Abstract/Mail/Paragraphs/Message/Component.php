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

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        if ($renderer && $renderer instanceof Kwf_Component_Renderer_Mail) {
            $ret['order'] = $renderer->getRecipient();
        }
        return $ret;
    }
}
