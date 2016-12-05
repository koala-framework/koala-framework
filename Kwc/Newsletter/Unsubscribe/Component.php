<?php
class Kwc_Newsletter_Unsubscribe_Component extends Kwc_Abstract_Composite_Component
{

    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        $ret['generators']['child']['component']['form'] = 'Kwc_Newsletter_Unsubscribe_Form_Component';
        $ret['placeholder']['headline'] = trlKwfStatic('Unsubscribe newsletter');
        $ret['flags']['skipFulltext'] = true;
        $ret['flags']['noIndex'] = true;
        $ret['flags']['processInput'] = true;
        $ret['flags']['passMailRecipient'] = true;
        return $ret;
    }

    public function processInput(array $postData)
    {
        if (!isset($postData['recipient'])) {
            throw new Kwf_Exception_NotFound();
        }
        $recipient = Kwc_Mail_Redirect_Component::parseRecipientParam($postData['recipient']);

        if (!($recipient instanceof Kwc_Mail_Recipient_UnsubscribableInterface)) {
            throw new Kwf_Exception("To unsubscribe from a newsletter, the recipient row must implement 'Kwc_Mail_Recipient_UnsubscribableInterface'");
        }
        $comp = $this->getData()->getChildComponent('-form')->getComponent();
        $comp->_recipient = $recipient;
        $comp->processInput($postData);
    }

}
