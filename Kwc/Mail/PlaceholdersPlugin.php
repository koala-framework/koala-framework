<?php
class Vpc_Mail_PlaceholdersPlugin extends Vps_Component_Plugin_Placeholders
{
    public function processMailOutput($output, Vpc_Mail_Recipient_Interface $recipient = null)
    {
        $placeholders = Vps_Component_Data_Root::getInstance()
            ->getComponentById($this->_componentId)
            ->getComponent()->getPlaceholders($recipient);
        foreach ($placeholders as $p=>$v) {
            $output = str_replace("%$p%", $v, $output);
        }
        return $output;
    }
}
