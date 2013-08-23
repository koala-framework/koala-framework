<?php
class Kwf_Component_View_Helper_Component extends Kwf_Component_View_Renderer
{
    protected static function _getGroupedViewPlugins(Kwf_Component_Data $component)
    {
        $plugins = array();
        foreach ($component->getComponent()->getActiveViewPlugins() as $p) {
            if (is_instance_of($p, 'Kwf_Component_Plugin_Interface_ViewBeforeCache')) {
                $plugins['beforeCache'][] = $p;
            } else if (is_instance_of($p, 'Kwf_Component_Plugin_Interface_ViewBeforeChildRender')) {
                $plugins['before'][] = $p;
            } else if (is_instance_of($p, 'Kwf_Component_Plugin_Interface_ViewAfterChildRender')) {
                $plugins['after'][] = $p;
            } else if (is_instance_of($p, 'Kwf_Component_Plugin_Interface_ViewReplace')) {
                $plugins['replace'][] = $p;
            } else if (is_instance_of($p, 'Kwf_Component_Plugin_Interface_UseViewCache')) {
                $plugins['useCache'][] = $p;
            }
        }
        return $plugins;
    }

    public function component(Kwf_Component_Data $component = null)
    {
        if (!$component) return '';
        $plugins = self::_getGroupedViewPlugins($component);
        $viewCacheSettings = $component->getComponent()->getViewCacheSettings();
        return $this->_getRenderPlaceholder(
            $component->componentId, array(), null, $plugins, $viewCacheSettings['enabled']
        );
    }

    public function render($componentId, $config)
    {
        $renderer = $this->_getRenderer();
        $component = $this->_getComponentById($componentId);

        $vars = $component->getComponent()->getTemplateVars($renderer);
        if (is_null($vars)) throw new Kwf_Exception('Return value of getTemplateVars() returns null. Maybe forgot "return $ret?"');

        $view = new Kwf_Component_View($renderer);
        $view->assign($vars);
        return $view->render($renderer->getTemplate($component, 'Component'));
    }

    public function getViewCacheSettings($componentId)
    {
        return $this->_getComponentById($componentId)->getComponent()->getViewCacheSettings();
    }
}
