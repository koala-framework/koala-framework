<?php
class Kwc_Menu_Mobile_Controller extends Kwf_Controller_Action
{
    public function jsonIndexAction()
    {
        if ($this->_getParam('subrootComponentId')) {
            $cacheId = 'kwcMenuMobile-root-' . $this->_getParam('subrootComponentId').'-'.$this->_getParam('class');
        } else if ($this->_getParam('pageId')) {
            $cacheId = 'kwcMenuMobile-' . $this->_getParam('pageId');
        }
        $data = Kwf_Cache_Simple::fetch($cacheId);
        if ($data === false) {
            if ($this->_getParam('subrootComponentId')) {
                $pages = $this->_getChildPages();
            } else if ($this->_getParam('pageId')) {
                $component = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_getParam('pageId'));
                $pages = $this->_getChildPagesRecursive(array($component), 2);
                foreach ($pages as $k=>$p) {
                    unset($pages[$k]['name']);
                    unset($pages[$k]['url']);
                }
            }
            $data = array(
                'lifetime' => 60*60,
                'mimeType' => 'application/json',
                'mtime' => time(),
                'contents' => json_encode(array(
                    'pages' => $pages
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

        foreach ($component->getChildComponents(array('flag' => 'menuCategory')) as $cat) {
            if (in_array($cat->id, $category)) $categoryComponents[$cat->id] = $cat;
        }

        $sortedCategoryComponents = array();

        foreach ($category as $c) {
            $sortedCategoryComponents[$c] = $categoryComponents[$c];
        }

        return $this->_getChildPagesRecursive($sortedCategoryComponents, 2);
    }

    protected function _getChildPagesRecursive($parentPage, $levels)
    {
        $levels--;
        $ret = array();
        $i = 0;
        if (!is_array($parentPage)) $parentPage = array($parentPage);
        foreach ($parentPage as $component) {
            $pages = $component->getChildPages(array('showInMenu'=>true));
            foreach($pages as $page) {
                if ($page->getDeviceVisible() == Kwf_Component_Data::DEVICE_VISIBLE_HIDE_ON_MOBILE
                ) {
                    continue;
                }

                $ret[$i]['name'] = $page->name;
                $ret[$i]['url'] = $page->url;
                $ret[$i]['id'] = $page->componentId;
                if ($levels > 0) {
                    $ret[$i]['children'] = $this->_getChildPagesRecursive($page, $levels);
                    $ret[$i]['hasChildren'] = !empty($ret[$i]['children']);
                } else {
                    foreach($page->getChildPages(array('showInMenu'=>true)) as $page) {
                        if ($page->getDeviceVisible() == Kwf_Component_Data::DEVICE_VISIBLE_HIDE_ON_MOBILE
                        ) {
                            continue;
                        }
                        $ret[$i]['hasChildren'] = true;
                        break;
                    }
                }

                if (Kwc_Abstract::getSetting($this->_getParam('class'), 'showSelectedPageInList') && !empty($ret[$i]['children']) &&
                    !is_instance_of($page->componentClass, 'Kwc_Basic_LinkTag_FirstChildPage_Component')) {
                    array_unshift($ret[$i]['children'], array(
                        'name' => $page->name,
                        'url' => $page->url,
                        'isParent' => true,
                        'hasChildren' => false
                    ));
                }
                $i++;
            }
        }
        return $ret;
    }
}
