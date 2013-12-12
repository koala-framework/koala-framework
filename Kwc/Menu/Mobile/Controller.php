<?php
class Kwc_Menu_Mobile_Controller extends Kwf_Controller_Action
{
    public function jsonIndexAction()
    {
        $cacheId = 'kwcMenuMobile-' . $this->_getParam('componentId');
        $data = Kwf_Cache_Simple::fetch($cacheId);
        if ($data === false) {
            $data = array(
                'lifetime' => 60*60,
                'mimeType' => 'application/json',
                'mtime' => time(),
                'contents' => json_encode(array(
                    'pages' => $this->_getChildPages()
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

    protected function _getChildPages()
    {
        $category = Kwc_Abstract::getSetting($this->_getParam('class'), 'level');
        if (is_string($category)) $category = array($category);
        $categoryClass = null;
        $component = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_getParam('subrootComponentId'));

        $categoryComponents = array();
        foreach($component->getChildComponents(array('flag' => 'menuCategory')) as $cat) {
            if (in_array($cat->id, $category)) $categoryComponents[] = $cat;
        }

        return $this->_getChildPagesRecursive($categoryComponents);
    }

    protected function _getChildPagesRecursive($parentPage)
    {
        $ret = array();
        $i = 0;
        if (!is_array($parentPage)) $parentPage = array($parentPage);
        foreach ($parentPage as $component) {
            $pages = $component->getChildPages(array('showInMenu'=>true));
            foreach($pages as $page) {
                if ($page->row
                    && isset($page->row->device_visible)
                    && $page->row->device_visible == Kwf_Component_Data::DEVICE_VISIBLE_HIDE_ON_MOBILE
                ) {
                    continue;
                }

                $ret[$i]['name'] = $page->name;
                $ret[$i]['url'] = $page->url;
                $ret[$i]['class'] = array();
                $ret[$i]['children'] = $this->_getChildPagesRecursive($page);

                if ($i == 0) $ret[$i]['class'][] = 'first';
                if ($i == count($pages)-1) $ret[$i]['class'][] = 'last';
                if (!empty($ret[$i]['children'])) $ret[$i]['class'][] = 'hasChildren';
                $ret[$i]['class'] = implode(' ', $ret[$i]['class']);

                $i++;
            }
        }
        return $ret;
    }
}
