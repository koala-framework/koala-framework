<?php
class Vps_View_Helper_Component
{
    public function component($componentId)
    {
        return '{nocache: ' . $componentId . '}';
    }
}
