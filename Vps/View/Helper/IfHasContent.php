<?php
class Vps_View_Helper_IfHasContent
{
    protected $_tag = 'content';

    public function ifHasContent(Vps_Component_Data $component = null)
    {
        static $componentId;
        static $counter;
        if (!$counter) $counter = array();

        $ret = '';
        if ($component) {
            if ($componentId) {
                throw new Vps_Exception("Helper IfHasContent must end component with id {$componentId} before creating a new one.");
            }
            $componentClass = $component->componentClass;
            if (!isset($counter[$componentClass])) $counter[$componentClass] = 0;
            $counter[$componentClass]++;
            $ret = "{{$this->_tag}: {$componentClass} {$component->componentId} {$counter[$componentClass]}}";
            $componentId = $component->componentId;
        } else if ($componentId) {
            $ret = "{{$this->_tag}}";
            $componentId = null;
        }
        echo $ret;
    }
}
