<?php
class Vps_View_Helper_Component
{
    public function component($componentId)
    {
        if ($componentId instanceof Vps_Dao_Row_TreeCache) {
            $componentId = $componentId->component_id;
        }
        return '{nocache: ' . $componentId . '}';
    }
}
