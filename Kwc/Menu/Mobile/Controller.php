<?php
class Kwc_Menu_Mobile_Controller extends Kwf_Controller_Action
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
        $cacheId = 'menuMobile' . $this->_getParam('componentId');
        $data = Kwf_Cache_Simple::fetch($cacheId);
        if ($data === false) {
            $data = array(
                'lifetime' => 60*60,
                'mimeType' => 'application/json',
                'mtime' => time(),
                'contents' => json_encode(array(
                    'pages' => $this->_getChildPages($categoryComponents, array(
                            'name' => trlKwf('back'),
                            'url' => '#',
                            'class' => 'back',
                            'children' => array()
                        ))
                ))
            );
            Kwf_Cache_Simple::add($cacheId, $data);
        }

        Kwf_Media_Output::output($data);
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

                if ($page->row && isset($page->row->device_visible)) $ret[$i]['class'][] = $page->row->device_visible;
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
        $component = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_getParam('rootComponentId'));

        $categoryComponents = array();
        foreach($component->getChildComponents(array('flag' => 'menuCategory')) as $cat) {
            if (in_array($cat->id, $category)) $categoryComponents[] = $cat;
        }
        return $categoryComponents;
    }
}
