<?php
class Vps_View_Helper_IfHasContent
{
    public function ifHasContent(Vps_Component_Data $component = null)
    {
        static $componentId;
        if ($component && $component->componentId != $componentId) {
            if ($componentId) {
                throw new Vps_Exception("Helper IfHasContent must end component with id {$componentId} before creating a new one.");
            }
            $ret = "{content: {$component->componentClass} {$component->componentId}}";
        } else {
            $ret = "{content}";
            $componentId = null;
        }
        echo $ret;
    }
}
