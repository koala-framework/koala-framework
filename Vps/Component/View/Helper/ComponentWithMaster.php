<?php
class Vps_Component_View_Helper_ComponentWithMaster extends Vps_Component_View_Helper_Component
{
    public function componentWithMaster(array $componentsWithMaster)
    {
        $last = array_pop($componentsWithMaster);

        $component = $last['data'];

        if ($last['type'] == 'master') {
            $innerComponent = $componentsWithMaster[0]['data'];
            $vars = array();
            $vars['component'] = $innerComponent;
            $vars['data'] = $innerComponent;
            $vars['componentsWithMastert'] = $componentsWithMaster;
            $vars['cssClass'] = Vpc_Abstract::getCssClass($component->componentClass);
            $vars['boxes'] = array();
            foreach ($innerComponent->getChildBoxes() as $box) {
                $vars['boxes'][$box->box] = $box;
            }
            $template = Vpc_Abstract::getTemplateFile($component->componentClass, 'Master');

            $view = new Vps_Component_View($this->_getRenderer());
            $view->assign($vars);
            return $view->render($template);
        } else if ($last['type'] == 'component') {
            $plugins = $component->getPlugins();
            return $this->_getRenderPlaceholder($component->componentId, array(), null, 'component', $plugins);
        } else {
            throw new Vps_Exception("invalid type");
        }
    }
}
