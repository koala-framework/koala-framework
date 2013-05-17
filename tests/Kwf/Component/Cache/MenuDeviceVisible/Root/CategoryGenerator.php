<?php
class Kwf_Component_Cache_MenuDeviceVisible_Root_CategoryGenerator extends Kwc_Root_Category_Generator
{
    protected function _init()
    {
        parent::_init();
        $this->_useMobileBreakpoints = true;
    }
}
