<?php
class Kwf_Component_OutputReplacePlugin_TestPlugin_Component extends Kwf_Component_Plugin_Abstract
    implements Kwf_Component_Plugin_Interface_ViewReplace
{
    public function replaceOutput($renderer)
    {
        if ($this->_componentId == 'root_test') return false;
        return 'replacement';
    }
}
