<?php
class Vps_Component_Output_Master
{
    private $_templateComponentId;

    public function render($component, $config, $view)
    {
        if (isset($config[0]) && $config[0] != '') {
            $template = $config[0];
        } else {
            $template = Vpc_Abstract::getTemplateFile($component->componentClass, 'Master');
            if (!$template) throw new Vps_Exception("No Component-Template found for '{$component->componentClass}'");
        }
        if (isset($config[1])) $this->_templateComponentId = $config[1];

        $vars = array();
        $vars['component'] = $component;
        $vars['data'] = $component;
        $vars['cssClass'] = Vpc_Abstract::getCssClass($component->componentClass);
        $vars['boxes'] = array();
        foreach ($component->getChildBoxes() as $box) {
            $vars['boxes'][$box->box] = $box;
        }

        $view->assign($vars);
        return $view->render($template);
    }

    public function getCacheValue()
    {
        return $this->_templateComponentId;
    }
}
