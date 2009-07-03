<?php
class Vps_Component_Output_ComponentMail extends Vps_Component_Output_ComponentMaster
{
    public function render($component, $type, Vpc_Mail_Recipient_Interface $recipient = null)
    {
        $this->setIgnoreVisible(true);
        // Normaler Output
        $template = Vpc_Admin::getComponentFile($component->componentClass, "Mail.$type", 'tpl');
        if (!$template) {
            throw new Vps_Exception("No Mail-Template found for '$component->componentClass'");
        }
        $templateVars = $component->getComponent()->getMailVars($recipient);
        if (is_null($templateVars)) {
            throw new Vps_Exception('Return value of getMailVars() returns null. Maybe forgot "return $ret?"');
        }
        return $this->_renderView($template, $templateVars);
    }
}
