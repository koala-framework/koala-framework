<?php
class Vps_Component_View_Helper_MailPartial extends Vps_Component_View_Helper_Partial
{
    protected function _getTemplate($componentClass, $config)
    {
        $template = Vpc_Abstract::getTemplateFile($componentClass, "Partial.{$config['type']}");
        if (!$template) {
            $template = Vpc_Abstract::getTemplateFile($componentClass, "Partial");
        }
        return $template;
    }
}
