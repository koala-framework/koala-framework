<?php
class Vps_View_Helper_Component extends Vps_View_Helper_Abstract
{
    public function component(Vps_Component_Data $component = null)
    {
        if (!$component) return '';

        $view = $this->_getView();

        $config = array();
        $type = 'component';
        $value = null;
        $plugins = array();

        // Box
        if (isset($component->box) && $component->box) {
            $type = 'box';
        }
        // Master
        if ($view && $type == 'component' && $view->getIsRenderMaster()) {
            $masterComponent = $view->getMasterComponent($component);
            if ($masterComponent) {
                $config = array($masterComponent->masterTemplate, $masterComponent->componentId);
                $type = 'master';
                $value = $masterComponent->componentId;
            }
        }
        // Component
        if ($view && $type == 'component') {
            // Plugins
            $plugins = $view->getPlugins($component);
            // ComponentMaster
            $componentMasterTemplate = $view->getComponentMasterTemplate($component, $view->getIsRenderMaster());
            if ($componentMasterTemplate) {
                $config = array($componentMasterTemplate);
                $type = 'master';
            }
        }

        $config = implode(' ', $config);
        $componentId = $component->componentId;
        if ($value) $componentId .= "($value)";
        if ($plugins) $componentId .= '[' . implode(' ', $plugins) . ']';
        return '{' . "$type: $componentId $config" . '}';
    }
}
