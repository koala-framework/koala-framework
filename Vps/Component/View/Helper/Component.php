<?php
class Vps_Component_View_Helper_Component extends Vps_Component_View_Renderer
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

    public function render($component, $config, $view)
    {
        $template = Vpc_Abstract::getTemplateFile($component->componentClass);
        if (!$template) throw new Vps_Exception("No Component-Template found for '{$component->componentClass}'");

        $vars = $component->getComponent()->getTemplateVars();
        if (is_null($vars)) throw new Vps_Exception('Return value of getTemplateVars() returns null. Maybe forgot "return $ret?"');

        $view->assign($vars);
        return $view->render($template);
    }

    protected function _saveMeta($component)
    {
        return $component->getComponent()->saveCache();
    }
}
