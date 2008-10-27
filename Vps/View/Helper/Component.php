<?php
class Vps_View_Helper_Component
{
    public function component(Vps_Component_Data $component = null)
    {
        if (!$component) return '';
        $plugins = $component->getPlugins('Vps_Component_Plugin_Interface_View');
        $plugins = implode(' ', $plugins);
        if ($plugins) $plugins = ' '.$plugins;
        return "{nocache: {$component->componentClass} {$component->componentId} {$plugins}}";
    }
}
