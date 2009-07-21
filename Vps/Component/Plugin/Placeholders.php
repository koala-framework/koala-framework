<?php
class Vps_Component_Plugin_Placeholders extends Vps_Component_Plugin_View_Abstract
{
    public function getExecutionPoint()
    {
        return Vps_Component_Plugin_Interface_View::EXECUTE_AFTER;
    }

    protected function _getPlaceholders()
    {
        return Vps_Component_Data_Root::getInstance()->getComponentById($this->_componentId)
            ->getComponent()->getPlaceholders();
    }

    public function processMailOutput($output, Vpc_Mail_Recipient_Interface $recipient)
    {
        return processOutput($output);
    }

    public function processOutput($output)
    {
        foreach ($this->_getPlaceholders() as $p=>$v) {
            $output = str_replace($p, $v, $output);
        }
        return $output;
    }
}
