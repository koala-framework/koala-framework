<?php
class Vps_View_Helper_HasContent extends Vps_View_Helper_Abstract
{
    public function hasContent(Vps_Component_Data $component = null)
    {
        return Vps_Component_Output_HasContent::getHelperOutput($this->_getView()->data, $component);
    }
}
