<?php
class Kwf_Component_Plugin_Placeholders extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewAfterChildRender
{
    protected function _getPlaceholders()
    {
        return Kwf_Component_Data_Root::getInstance()->getComponentById($this->_componentId)
            ->getComponent()->getPlaceholders();
    }

    public function processOutput($output, $renderer)
    {
        foreach ($this->_getPlaceholders() as $p=>$v) {
            $output = str_replace("%$p%", $v, $output);
        }
        return $output;
    }
}
