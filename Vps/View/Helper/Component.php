<?php
class Vps_View_Helper_Component
{
    public function component($componentId)
    {
        if ($componentId instanceof Vps_Component_Data) {
            $componentId = $componentId->componentId;
        }
        return '{nocache: ' . $componentId . '}';
    }
}
