<?php
class Kwc_Statistics_CookieBeforePlugin extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewBeforeCache
{
    public function processOutput($output, $renderer)
    {
        $component = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_componentId);
        $output = '{kwcOptType}' . Kwf_Statistics::getOptType($component) . '{/kwcOptType}' . $output;
        return $output;
    }
}
