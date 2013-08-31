<?php
class RedMallee_Menu_MainVertical_Controller extends Kwf_Controller_Action
{
    protected $_pages = array();
    protected $_componentSelect;

    public function jsonIndexAction()
    {
        $categoryComponents = $this->_getCategoryComponent();

        $select = new Kwf_Component_Select();
        $select->whereShowInMenu(true);
        $select->ignoreVisible(true);
        $this->_componentSelect = $select;

        $this->view->pages = $this->_getChildPages($categoryComponents, array(
            'name' => trl('back'),
            'url' => '#',
            'class' => 'back',
            'children' => array()
        ));
    }

    protected function _isAllowedComponent()
    {
        return true;
    }

    protected function _getChildPages($categoryComponents, $back = null)
    {
        $ret = array();
        $i = 0;
        if (!is_array($categoryComponents)) $categoryComponents = array($categoryComponents);
        foreach ($categoryComponents as $component) {
            $pages = $component->getChildPages($this->_componentSelect);
            foreach($pages as $page) {
                $ret[$i]['name'] = $page->name;
                $ret[$i]['url'] = $page->url;
                $ret[$i]['class'] = array();
                $ret[$i]['children'] = $this->_getChildPages($page, $back);
                if (!empty($ret[$i]['children']) && $back) {
                    array_unshift($ret[$i]['children'], array(
                        'name' => $page->name,
                        'url' => $page->url,
                        'class' => '',
                        'children' => array()
                    ));
                    array_unshift($ret[$i]['children'], $back);
                }

                if ($i == 0) $ret[$i]['class'][] = 'first';
                if ($i == count($pages)-1) $ret[$i]['class'][] = 'last';
                if (!empty($ret[$i]['children'])) $ret[$i]['class'][] = 'hasDropdown';
                $ret[$i]['class'] = implode(' ', $ret[$i]['class']);

                $i++;
            }
        }
        return $ret;
    }

    protected function _getCategoryComponent()
    {
        $category = Kwc_Abstract::getSetting($this->_getParam('class'), 'level');
        if (is_string($category)) $category = array($category);
        $categoryClass = null;
        $component = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_getParam('componentId'));
        do {
            if (Kwc_Abstract::getFlag($component->componentClass, 'menuCategory')) {
                $categoryClass = $component->componentClass;
                break;
            }
        } while ($component = $component->parent);

        $categoryComponents = array();
        foreach(Kwf_Component_Data_Root::getInstance()->getComponentsByClass($categoryClass, array('subroot' => $component)) as $cat) {
            if (in_array($cat->id, $category)) $categoryComponents[] = $cat;
        }
        return $categoryComponents;
    }
}
