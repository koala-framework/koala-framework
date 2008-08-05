<?php
class Vps_View_Helper_IfHasContent
{
    public function ifHasContent(Vps_Component_Data $component = null)
    {
        static $componentId;
        if ($component) {
            if ($componentId) {
                throw new Vps_Exception("Helper IfHasContent must end component with id {$componentId} before creating a new one.");
            }
            $componentId = $component->componentId;
            $ret = "{content: {$component->componentClass} {$componentId}}";
        } else {
            $ret = "{content}";
            $componentId = null;
        }
        echo $ret;
    }
}
