<?php
class Vps_View_Helper_Component
{
    public function component(Vps_Component_Data $component = null)
    {
        if (!$component) return '';

        if (isset($component->box) && $component->box) {
            return Vps_Component_Output_Box::getHelperOutput($component);
        } else {
            return Vps_Component_Output_Component::getHelperOutput($component);
        }
    }
}
