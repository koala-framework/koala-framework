<?php
class Vps_Component_Output_ComponentMaster extends Vps_Component_Output_NoCache
{
    public function render($component)
    {
        // Normaler Output
        $template = $component->getComponent()->getTemplateFile();
        if (!$template) {
            throw new Vps_Exception("No Component-Template found for '$componentClass'");
        }
        $templateVars = $component->getComponent()->getTemplateVars();
        if (is_null($templateVars)) {
            throw new Vps_Exception('Return value of getTemplateVars() returns null. Maybe forgot "return $ret?"');
        }
        $ret = $this->_renderView($template, $templateVars);

        // Falls es ein Master-Template gibt und wir nicht bei der Root sind, das Master-Template dazurendern
        $template = $component->getComponent()->getTemplateFile('Master');
        if ($template && Vps_Component_Data_Root::getInstance()->componentId != $component->componentId) {
            $templateVars = array();
            $templateVars['component'] = $component;
            $templateVars['boxes'] = array();
            foreach ($component->getChildBoxes() as $box) {
                $templateVars['boxes'][$box->box] = $box;
            }
            $content = $this->_renderView($template, $templateVars);

            $componentClass = $component->componentClass;
            $componentId = $component->componentId;
            preg_match_all("/{nocache: $componentClass $componentId ?([^}]*)}/", $content, $matches);
            foreach ($matches[0] as $key => $search) {
                $plugins = $matches[1][$key] ? explode(' ', trim($matches[1][$key])) : array(); // TODO Plugins?
                //$replace = $this->_processComponent($componentId, $componentClass, false, $plugins);
                $ret = str_replace($search, $ret, $content);
            }
        }
        return $ret;
    }
}
