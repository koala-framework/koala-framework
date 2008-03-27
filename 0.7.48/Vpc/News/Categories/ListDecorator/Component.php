<?php
class Vpc_News_Categories_ListDecorator_Component extends Vpc_Decorator_Abstract
{

    private function _searchNewsComponentByNewsInterface($component)
    {
        $ret = null;

        if ($component instanceof Vpc_News_Interface_Component) {
            $ret = $component;
        }

        if (is_null($ret)) {
            $childComps = $component->getChildComponents();
            if ($childComps) {
                foreach ($childComps as $childComp) {
                    if ($childComp instanceof Vpc_News_Interface_Component) {
                        $ret = $childComp;
                        break;
                    }
                    // rekursion
                    $rekursiv = $this->_searchNewsComponentByNewsInterface($childComp);
                    if (!is_null($rekursiv)) {
                        $ret = $rekursiv;
                        break;
                    }
                }
            }
        }

        return $ret;
    }

    public function getTemplateVars()
    {
        $vars = parent::getTemplateVars();
        $vars['categoryComponents'] = array();

        $newsSubComponent = $this->_searchNewsComponentByNewsInterface($this->getComponent());
        if (!is_null($newsSubComponent)) {
            $newsComponent = $newsSubComponent->getNewsComponent();

            if ($newsComponent) {
                $categoryPages = $newsComponent->getPageFactory()->getCategoryPages();
                foreach ($categoryPages as $categoryPage) {
                    $vars['categoryComponents'][] = $categoryPage->getTemplateVars();
                }
            }
        }

        return $vars;
    }
}
