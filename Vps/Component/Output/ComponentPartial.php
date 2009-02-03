<?php
class Vps_Component_Output_ComponentPartial extends Vps_Component_Output_NoCache
{
    public function render($component, $partial, $id, $info)
    {
        // Normaler Output
        $componentClass = $component->componentClass;
        $template = Vpc_Admin::getComponentFile($componentClass, 'Partial', 'tpl');
        if (!$template) {
            throw new Vps_Exception("No Partial-Template found for '$componentClass'");
        }
        $templateVars = $component->getComponent()->getPartialVars($partial, $id, $info);
        if (is_null($templateVars)) {
            throw new Vps_Exception('Return value of getTemplateVars() returns null. Maybe forgot "return $ret?"');
        }
        return $this->_renderView($template, $templateVars);
    }
}
