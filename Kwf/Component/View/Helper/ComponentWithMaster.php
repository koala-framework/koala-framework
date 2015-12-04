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

            $vars = $component->getComponent()->getMasterTemplateVars($innerComponent, $this->_getRenderer());
            $vars['componentWithMaster'] = $componentWithMaster;

            $masterTemplate = $this->_getRenderer()->getTemplate($component, 'Master');
            if (substr($masterTemplate, -4) == '.tpl') {
                $view = new Kwf_Component_View($this->_getRenderer());
                $view->assign($vars);
                $ret = $view->render($masterTemplate);
            } else {
                $twig = new Kwf_Component_Renderer_Twig_Environment($this->_getRenderer());
                $ret = $twig->render($masterTemplate, $vars);
            }
            $ret = $this->_replaceKwfUp($ret);
            return $ret;
        } else if ($last['type'] == 'component') {
            $helper = new Kwf_Component_View_Helper_Component();
            $helper->setRenderer($this->_getRenderer());
            $kwfUniquePrefix = Kwf_Config::getValue('application.uniquePrefix');
            if ($kwfUniquePrefix) $kwfUniquePrefix = $kwfUniquePrefix.'-';
            return '<div class="'.$kwfUniquePrefix.'kwfMainContent">' . "\n    " .
                $helper->component($component) . "\n" .
                '</div><!--/'.$kwfUniquePrefix.'kwfMainContent-->' . "\n";
        } else {
            throw new Kwf_Exception("invalid type");
        }
    }
}
