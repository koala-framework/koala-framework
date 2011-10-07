<?php
class Kwf_Component_Plugin_Placeholders extends Kwf_Component_Plugin_View_Abstract
{
    public function getExecutionPoint()
    {
        return Kwf_Component_Plugin_Interface_View::EXECUTE_AFTER;
    }

    protected function _getPlaceholders()
    {
        return Kwf_Component_Data_Root::getInstance()->getComponentById($this->_componentId)
            ->getComponent()->getPlaceholders();
    }

    public function processOutput($output)
    {
        foreach ($this->_getPlaceholders() as $p=>$v) {
            $output = str_replace("%$p%", $v, $output);
        }
        return $output;
    }
}
