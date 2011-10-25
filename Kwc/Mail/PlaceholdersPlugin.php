<?php
class Kwc_Mail_PlaceholdersPlugin extends Kwf_Component_Plugin_Placeholders
{
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
}
