<?php
class Kwf_Component_View_Helper_Component extends Kwf_Component_View_Renderer
{
    public function component(Kwf_Component_Data $component = null)
    {
        if (!$component) return '';

        $renderer = $this->_getRenderer();
        $config = array();
        $type = 'component';

        if ($renderer instanceof Kwf_Component_Renderer_Mail) {
            $type = 'mail';
            $config = array(
                'type' => $renderer->getRenderFormat()
            );
            $plugins = array();
        } else {
            $plugins = $component->getPlugins(); // Plugins werden bei Mail nicht ausgeführt, weil die manuell durchgegangen werden, damit der Recipient übergeben werden kann
        }
        return $this->_getRenderPlaceholder(
            $component->componentId, $config, null, $type, $plugins
        );
    }

    public function render($componentId, $config)
    {
        $component = $this->_getComponentById($componentId);
        $template = Kwc_Abstract::getTemplateFile($component->componentClass);
        if (!$template) throw new Kwf_Exception("No Component-Template found for '{$component->componentClass}'");

        $vars = $component->getComponent()->getTemplateVars();
        if (is_null($vars)) throw new Kwf_Exception('Return value of getTemplateVars() returns null. Maybe forgot "return $ret?"');

        $view = new Kwf_Component_View($this->_getRenderer());
        $view->assign($vars);
        return $view->render($template);
    }
}
