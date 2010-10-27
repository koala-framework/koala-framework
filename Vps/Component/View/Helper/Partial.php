<?php
class Vps_Component_View_Helper_Partial extends Vps_Component_View_Renderer
{
    public function render($componentId, $config)
    {
        $component = $this->getComponent($componentId);
        $partialsClass = $config['class'];
        $partial = new $partialsClass($config['params']);

        // Normaler Output
        $componentClass = $component->componentClass;
        $template = Vpc_Abstract::getTemplateFile($componentClass, 'Partial');
        if (!$template) {
            throw new Vps_Exception("No Partial-Template found for '$componentClass'");
        }
        $vars = $component->getComponent()->getPartialVars($partial, $config['id'], $config['info']);
        if (is_null($vars)) {
            throw new Vps_Exception('Return value of getPartialVars() returns null. Maybe forgot "return $ret?"');
        }
        $vars['info'] = $config['info'];
        $vars['data'] = $component;
        $view = $this->_getView();
        $view->assign($vars);
        return $view->render($template);
    }
}
