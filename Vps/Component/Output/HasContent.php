<?php
class Vps_Component_Output_HasContent
{
    public static function getHelperOutput(Vps_Component_Data $component)
    {
        return $component->getComponent()->hasContent();
    }
}
