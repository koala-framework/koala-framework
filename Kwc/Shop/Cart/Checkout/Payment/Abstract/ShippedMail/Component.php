<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_ShippedMail_Component extends Kwc_Mail_Editable_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['content']['component'] = 'Kwc_Shop_Cart_Checkout_Payment_Abstract_ShippedMail_Paragraphs_Component';
        return $ret;
    }

    public function getRecipientSources()
    {
        return array(
            'ord' => get_class(Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->getData()->getParentByClass('Kwc_Shop_Cart_Component')->componentClass, 'childModel'))
                ->getReferencedModel('Order'))
        );
    }

    public function getNameForEdit()
    {
        return trlKwf('Shop Shipped Mail') . ' '
            . Kwf_Trl::getInstance()->trlStaticExecute(Kwc_Abstract::getSetting($this->getData()->parent->componentClass, 'componentName'));
    }

    public function getPlaceholders(Kwc_Mail_Recipient_Interface $recipient = null)
    {
        $ret = parent::getPlaceholders($recipient);
        if ($recipient) {
            $ret = array_merge($ret, $recipient->getPlaceholders());
        }
        return $ret;
    }
}
