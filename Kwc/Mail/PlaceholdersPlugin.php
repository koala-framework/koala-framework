<?php
class Kwc_Mail_PlaceholdersPlugin extends Kwf_Component_Plugin_Placeholders
{
    //called by Kwc_Mail_Abstract_Component::getHtml and getText
    public function processMailOutput($output, Kwc_Mail_Recipient_Interface $recipient = null)
    {
        $placeholders = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($this->_componentId)
            ->getComponent()->getPlaceholders($recipient);
        foreach ($placeholders as $p=>$v) {
            $output = str_replace("%$p%", $v, $output);
        }
        return $output;
    }

    //called during rendering as view plugin
    //recipient is set when showing mail in browser, not when sending mail
    public function processOutput($output, $renderer)
    {
        $output = parent::processOutput($output, $renderer);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_componentId, array('ignoreVisible' => true));
        $recipient = $c->getComponent()->getRecipient();
        $redirectComponent = $c->getChildComponent('_redirect');
        if ($redirectComponent && $recipient) {
            $redirectComponent = $redirectComponent->getComponent();
            $output = $redirectComponent->replaceLinks($output, $recipient, 'html');
        }
        return $output;
    }
}
