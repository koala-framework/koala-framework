<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_Mail_Component extends Kwc_Mail_Editable_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content']['component'] = 'Kwc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Component';
        $ret['recipientSources'] = array(
            'ord' => 'Kwc_Shop_Cart_Orders'
        );
        return $ret;
    }

    public function getNameForEdit()
    {
        return trlKwf('Shop Confirmation Text') . ' '
            . Kwf_Trl::getInstance()->trlStaticExecute(Kwc_Abstract::getSetting($this->getData()->parent->componentClass, 'componentName'));
    }

    public function getPlaceholders(Kwc_Shop_Cart_Order $o = null)
    {
        $ret = parent::getPlaceholders($o);
        if ($o) {
            $ret = array_merge($ret, $o->getPlaceholders());
        }
        return $ret;
    }
}
