<?php
class Kwc_Newsletter_Unsubscribe_Component extends Kwc_Abstract_Composite_Component
{

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        $ret['generators']['child']['component']['form'] = 'Kwc_Newsletter_Unsubscribe_Form_Component';
        $ret['placeholder']['headline'] = trlKwfStatic('Unsubscribe newsletter');
        $ret['flags']['skipFulltext'] = true;
        $ret['flags']['noIndex'] = true;
        return $ret;
    }

    public function processMailRedirectInput($recipient, $params)
    {
        if (!($recipient instanceof Kwc_Mail_Recipient_UnsubscribableInterface)) {
            throw new Kwf_Exception("To unsubscribe from a newsletter, the recipient row must implement 'Kwc_Mail_Recipient_UnsubscribableInterface'");
        }
        $comp = $this->getData()->getChildComponent('-form')->getComponent();
        $comp->_recipient = $recipient;
        $comp->processInput($params);
    }

}
