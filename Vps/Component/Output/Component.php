<?php
class Vps_Component_Output_Component extends Vps_Component_Output_Abstract
{
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
