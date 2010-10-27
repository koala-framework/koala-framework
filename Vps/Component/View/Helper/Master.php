<?php
class Vps_Component_View_Helper_Master extends Vps_Component_View_Renderer
{
    public function render($componentId, $config)
    {
        $component = $this->getComponent($componentId);

        $vars = array();
        $vars['component'] = $component;
        $vars['data'] = $component;
        $vars['cssClass'] = Vpc_Abstract::getCssClass($component->componentClass);
        $vars['boxes'] = array();
        foreach ($component->getChildBoxes() as $box) {
            $vars['boxes'][$box->box] = $box;
        }

        $view = $this->_getView();
        $view->assign($vars);
        return $view->render($config['template']);
    }
}
