<?php
class Kwc_Blog_Directory_Trl_Admin extends Kwc_Directories_Item_Directory_Trl_Admin
{
    protected function _getPluginParentComponents()
    {
        $detail = Kwc_Abstract::getChildComponentClass($this->_class, 'detail');
        return array($detail, $this->_class);
    }
}
