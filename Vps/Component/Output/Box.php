<?php
class Vps_Component_Output_Box extends Vps_Component_Output_Component
{
    public static function getHelperOutput(Vps_Component_Data $component)
    {
        return "{box: {$component->componentId}}";
    }
}
