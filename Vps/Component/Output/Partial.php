<?php
class Vps_Component_Output_Partial
{
    public function render($component, $config, $view)
    {
        $partialsClass = $config[0];
        $partial = new $partialsClass(unserialize(base64_decode(($config[1]))));
        $id = $config[2];
        $info = unserialize(base64_decode(($config[3])));

        // Normaler Output
        $componentClass = $component->componentClass;
        $template = Vpc_Abstract::getTemplateFile($componentClass, 'Partial');
        if (!$template) {
            throw new Vps_Exception("No Partial-Template found for '$componentClass'");
        }
        $vars = $component->getComponent()->getPartialVars($partial, $id, $info);
        if (is_null($vars)) {
            throw new Vps_Exception('Return value of getPartialVars() returns null. Maybe forgot "return $ret?"');
        }
        $vars['info'] = $info;
        $vars['data'] = $component;
        $view->setParam('info', $info);
        $view->assign($vars);
        return $view->render($template);
    }
}
