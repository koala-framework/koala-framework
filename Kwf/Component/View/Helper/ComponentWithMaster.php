<?php
class Kwf_Component_View_Helper_ComponentWithMaster extends Kwf_Component_View_Helper_Component
{
    public static function _sortByPriority(Kwf_Component_Data $c1, Kwf_Component_Data $c2)
    {
        if ($c1->priority == $c2->priority) {
            return 0;
        }
        return ($c1->priority > $c2->priority) ? -1 : 1;
    }

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
            $vars['boxes'] = array();
            foreach ($innerComponent->getPageOrRoot()->getChildBoxes() as $box) {
                $vars['boxes'][$box->box] = $box;
            }

            $vars['multiBoxes'] = array();
            foreach ($innerComponent->getPageOrRoot()->getRecursiveChildComponents(array('multiBox'=>true)) as $box) {
                $vars['multiBoxes'][$box->box][] = $box;
            }
            //sort by priority
            foreach ($vars['multiBoxes'] as $box=>$components) {
                usort($vars['multiBoxes'][$box], array('Kwf_Component_View_Helper_ComponentWithMaster', '_sortByPriority'));
            }

            $vars['cssClass'] = 'frontend';
            $cls = Kwc_Abstract::getSetting($component->componentClass, 'processedCssClass');
            foreach (explode(' ', $cls) as $i) {
                 $vars['cssClass'] .= ' master'.ucfirst($i);
            }

            $view = new Kwf_Component_View($this->_getRenderer());
            $view->assign($vars);
            if (Kwc_Abstract::hasSetting($component->componentClass, 'masterTemplate')) {
                $masterTemplate = Kwc_Abstract::getSetting($component->componentClass, 'masterTemplate');
            } else {
                $masterTemplate = $this->_getRenderer()->getTemplate($component, 'Master');
            }
            return $view->render($masterTemplate);
        } else if ($last['type'] == 'component') {
            $helper = new Kwf_Component_View_Helper_Component();
            $helper->setRenderer($this->_getRenderer());
            return '<div class="kwfMainContent">' . "\n    " .
                $helper->component($component) . "\n" .
                '</div>' . "\n";
        } else {
            throw new Kwf_Exception("invalid type");
        }
    }
}
