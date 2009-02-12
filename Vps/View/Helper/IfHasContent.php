<?php
class Vps_View_Helper_IfHasContent
{
    public function ifHasContent(Vps_Component_Data $component = null)
    {
        static $componentId;
        static $counter;
        if (!$counter) $counter = array();

        if ($component && $component->componentId != $componentId) {
            if ($componentId) {
                throw new Vps_Exception("Helper IfHasContent must end component with id {$componentId} before creating a new one.");
            }
            $componentClass = $component->componentClass;
            if (!isset($counter[$componentClass])) $counter[$componentClass] = 0;
            $counter[$componentClass]++;
            $ret = "{content: {$componentClass} {$component->componentId} {$counter[$componentClass]}}";
        } else {
            $ret = "{content}";
            $componentId = null;
        }
        echo $ret;
    }
}
