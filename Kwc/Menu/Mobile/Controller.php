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
        foreach (Kwf_Component_Data_Root::getInstance()->getPlugins('Kwf_Component_PluginRoot_Interface_PreDispatch') as $plugin) {
            $plugin->preDispatch($this->_getParam('pageUrl'));
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

        $skipProcessPages = true;
        foreach (Kwf_Component_Data_Root::getInstance()->getPlugins('Kwf_Component_PluginRoot_Interface_MaskComponent') as $plugin) {
            if (!$plugin->canIgnoreMasks()) {
                $skipProcessPages = false;
            }
        }
        foreach (Kwf_Component_Data_Root::getInstance()->getPlugins('Kwf_Component_PluginRoot_Interface_PostRender') as $plugin) {
            if (!$plugin->canIgnoreProcessUrl()) {
                $skipProcessPages = false;
            }
        }
        if (!$skipProcessPages) {
            $contents = json_decode($data['contents'], true);
            $contents['pages'] = $this->_processPages($contents['pages']);
            $data['contents'] = json_encode($contents);
        }

        Kwf_Media_Output::output($data);
    }

    protected function _processPages($pages)
    {
        $ret = array();
        foreach ($pages as $page) {
            if (isset($page['mask'])) {
                foreach (Kwf_Component_Data_Root::getInstance()->getPlugins('Kwf_Component_PluginRoot_Interface_MaskComponent') as $plugin) {
                    if (!$plugin->showMasked($page['mask']['type'], $page['mask']['params'])) {
                        continue 2; //don't show this page
                    } else {
                        unset($page['hidden']);
                    }
                }
            }

            if (isset($page['children'])) {
                $page['children'] = $this->_processPages($page['children']);
            }
            if (isset($page['url'])) {
                foreach (Kwf_Component_Data_Root::getInstance()->getPlugins('Kwf_Component_PluginRoot_Interface_PostRender') as $plugin) {
                    $page['url'] = $plugin->processUrl($page['url']);
                }
            }
            $ret[] = $page;
        }
        return $ret;
    }

    protected function _isAllowedComponent()
    {
        return true;
    }

    protected function _validateCsrf()
    {
        // Not necessary for Frontend
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

    protected function _getPageData($page)
    {
        return array(
            'name' => $page->name,
            'url' => $page->url,
            'id' => $page->componentId
        );
    }

    protected function _showPage(Kwf_Component_Data $page)
    {
        if ($page->getDeviceVisible() == Kwf_Component_Data::DEVICE_VISIBLE_HIDE_ON_MOBILE) {
            return false;
        }
        return true;
    }

    protected function _getChildPagesRecursive($parentPage, $levels)
    {
        $levels--;
        $ret = array();
        $i = 0;
        if (!is_array($parentPage)) $parentPage = array($parentPage);
        foreach ($parentPage as $component) {
            $pages = $component->getChildPages(array('showInMenu'=>true));
            foreach ($pages as $page) {
                if (!$this->_showPage($page)) {
                    continue;
                }

                $pageData = $this->_getPageData($page);
                if (!$pageData['url']) {
                    //skip pages without url (eg. invalid intern link) like componentLink does
                    continue;
                }
                $ret[$i]['name'] = $pageData['name'];
                $ret[$i]['url'] = $pageData['url'];
                $ret[$i]['id'] = $pageData['id'];
                foreach (Kwf_Component_Data_Root::getInstance()->getPlugins('Kwf_Component_PluginRoot_Interface_MaskComponent') as $plugin) {
                    $mask = $plugin->getMask($page);
                    if ($mask != Kwf_Component_PluginRoot_Interface_MaskComponent::MASK_TYPE_NOMASK) {
                        $ret[$i]['mask'] = $mask;
                        if ($ret[$i]['mask']['type'] == Kwf_Component_PluginRoot_Interface_MaskComponent::MASK_TYPE_HIDE) {
                            $ret[$i]['hidden'] = true;
                        }
                    }
                }

                if ($levels > 0) {
                    $ret[$i]['children'] = $this->_getChildPagesRecursive($page, $levels);
                    $ret[$i]['hasChildren'] = !empty($ret[$i]['children']);
                } else {
                    foreach ($page->getChildPages(array('showInMenu'=>true)) as $childPage) {
                        if (!$this->_showPage($childPage)) {
                            continue;
                        }
                        $ret[$i]['hasChildren'] = true;
                        break;
                    }
                }

                if (Kwc_Abstract::getSetting($this->_getParam('class'), 'showSelectedPageInList') && !empty($ret[$i]['children'])
                    && !is_instance_of($page->componentClass, 'Kwc_Basic_LinkTag_FirstChildPage_Component')
                    && !is_instance_of($page->componentClass, 'Kwc_Basic_LinkTag_FirstChildPage_Trl_Component')
                ) {
                    array_unshift($ret[$i]['children'], array(
                        'name' => $pageData['name'],
                        'url' => $pageData['url'],
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
