<?php
class Vps_Component_Output_HasContent
{
    public static function getHelperOutput(Vps_Component_Data $source, Vps_Component_Data $target)
    {
        return $target->getComponent()->hasContent();
    }
}
