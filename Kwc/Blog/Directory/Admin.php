<?php
class Kwc_Blog_Directory_Admin extends Kwc_Directories_Item_Directory_Admin
{
    protected function _getPluginParentComponents()
    {
        $detail = Kwc_Abstract::getChildComponentClass($this->_class, 'detail');
        return array($detail, $this->_class);
    }
}
