<?php
class Kwf_Component_View_Helper_ComponentWithMaster extends Kwf_Component_View_Helper_Component
{
    public function componentWithMaster(array $componentWithMaster)
    {
        $last = array_pop($componentWithMaster);

        $component = $last['data'];

        if ($last['type'] == 'master') {
            $innerComponent = $componentWithMaster[0]['data'];
            $vars = array();
            $vars['component'] = $innerComponent;
            $vars['data'] = $innerComponent;
            $vars['componentWithMaster'] = $componentWithMaster;
            $vars['cssClass'] = Kwc_Abstract::getCssClass($component->componentClass);
            $vars['boxes'] = array();
            foreach ($innerComponent->getPageOrRoot()->getChildBoxes() as $box) {
                $vars['boxes'][$box->box] = $box;
            }

            $view = new Kwf_Component_View($this->_getRenderer());
            $view->assign($vars);
            return $view->render($this->_getRenderer()->getTemplate($component, 'Master'));
        } else if ($last['type'] == 'component') {
            $plugins = self::_getGroupedViewPlugins($component->componentClass);
            return '<div class="kwfMainContent" style="width: ' . $component->getComponent()->getContentWidth() . 'px">' . "\n    " .
                $this->_getRenderPlaceholder($component->componentId, array(), null, 'component', $plugins) . "\n" .
                '</div>' . "\n";
        } else {
            throw new Kwf_Exception("invalid type");
        }
    }
}
