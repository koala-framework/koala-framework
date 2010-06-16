<?php
class Vps_Component_Output_Component
{
    public function render($component, $config)
    {
        $template = Vpc_Abstract::getTemplateFile($component->componentClass);
        if (!$template) throw new Vps_Exception("No Component-Template found for '{$component->componentClass}'");

        $vars = $component->getComponent()->getTemplateVars();
        if (is_null($vars)) throw new Vps_Exception('Return value of
            getTemplateVars() returns null. Maybe forgot "return $ret?"');

        $view = new Vps_Component_View();
        $view->assign($vars);
        return $view->render($template);
    }

    public static function getHelperOutput(Vps_Component_Data $component)
    {
        $plugins = $component->getPlugins('Vps_Component_Plugin_Interface_View');
        $plugins = implode(' ', $plugins);
        if ($plugins) $plugins = ' '.$plugins;
        $componentId = $component->componentId;
        return "{component: $componentId$plugins}";
    }
}
