<?php
class Vps_View_Helper_Component
{
    public function component($componentId, $renderDirectly = false)
    {
        return Vps_View_Component::renderCachedComponent($componentId, false, $renderDirectly);
    }
}
