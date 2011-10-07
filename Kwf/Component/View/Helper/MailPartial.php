<?php
class Kwf_Component_View_Helper_MailPartial extends Kwf_Component_View_Helper_Partial
{
    protected function _getTemplate($componentClass, $config)
    {
        $template = Kwc_Abstract::getTemplateFile($componentClass, "Partial.{$config['type']}");
        if (!$template) {
            $template = Kwc_Abstract::getTemplateFile($componentClass, "Partial");
        }
        return $template;
    }
}
