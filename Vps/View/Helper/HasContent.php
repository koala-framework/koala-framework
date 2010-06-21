<?php
class Vps_View_Helper_HasContent
{
    public function hasContent(Vps_Component_Data $component = null)
    {
        return Vps_Component_Output_HasContent::getHelperOutput($component);
    }
}
