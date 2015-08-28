<?php
class Kwf_Component_View_Helper_Component extends Kwf_Component_View_Renderer
{
    public function component(Kwf_Component_Data $component = null)
    {
        if (!$component) return '';
        $viewCacheSettings = $component->getComponent()->getViewCacheSettings();
        return $this->_getRenderPlaceholder(
            $component->componentId, array(), null, $viewCacheSettings['enabled']
        );
    }

    public function render($componentId, $config)
    {
        $renderer = $this->_getRenderer();
        $component = $this->_getComponentById($componentId);

        $vars = $component->getComponent()->getTemplateVars($renderer);
        if (is_null($vars)) throw new Kwf_Exception('Return value of getTemplateVars() returns null. Maybe forgot "return $ret?"');

        if (isset($vars['template'])) {
            $tpl = $vars['template'];
        } else {
            $tpl = $renderer->getTemplate($component, 'Component');
        }
        if (!$tpl) throw new Kwf_Exception("No template found for '$component->componentClass'");
        if (substr($tpl, -4) == '.tpl') {
            $view = new Kwf_Component_View($renderer);
            $view->assign($vars);
            $ret = $view->render($tpl);
        } else {
            $twig = new Kwf_Component_Renderer_Twig_Environment($renderer);
            $ret = $twig->render($tpl, $vars);
        }
        $ret = self::_replaceKwfUp($ret);
        return $ret;
    }

    public function getViewCacheSettings($componentId)
    {
        return $this->_getComponentById($componentId)->getComponent()->getViewCacheSettings();
    }
}
