<?php
class Vps_Component_Output_Mail extends Vps_Component_Output_Abstract
{
    public function render($component, $config, $view)
    {
        $type = $config[0];
        $recipient = $config[1];

        // Normaler Output
        $template = Vpc_Admin::getComponentFile($component->componentClass, "Mail.$type", 'tpl');
        if (!$template) {
            $template = Vpc_Admin::getComponentFile($component->componentClass, 'Component', 'tpl');
        }
        $vars = $component->getComponent()->getMailVars($recipient);
        if (is_null($vars)) {
            throw new Vps_Exception('Return value of getMailVars() returns null. Maybe forgot "return $ret?"');
        }
        $view->assign($vars);
        return $view->render($template);
    }
}
