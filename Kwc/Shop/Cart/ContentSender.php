<?php
class Kwc_Shop_Cart_ContentSender extends Kwf_Component_Abstract_ContentSender_Default
{
    //overridden to disable cache
    protected function _getProcessInputComponents($includeMaster)
    {
        return self::_findProcessInputComponents($this->_data);
    }
}
