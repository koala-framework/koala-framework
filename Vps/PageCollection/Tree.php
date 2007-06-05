<?php
class Vps_PageCollection_Tree extends Vps_PageCollection_Abstract
{
    protected $_pageParentIds = array();

    protected function _removePage($id)
    {
        if (isset($this->_pageParentIds[$id])) {
            unset($this->_pageParentIds[$id]);
        }
        return parent::_removePage($id);
    }

    public function setParentPage(Vpc_Interface $page, Vpc_Interface $parentPage)
    {
        $id = $page->getId();
        $parentId = $parentPage->getId();
        $rootId = $this->getRootPage()->getId();

        if ($parentId == $id) {
          throw new Vps_PageCollection_Exception('Cannot set Parent Page for the same object.');
        }

        if (!isset($this->_pages[$id])) {
          throw new Vps_PageCollection_Exception('Page does not exist.');
        }

        if (!isset($this->_pages[$parentId]) && $rootId != $parentId) {
          throw new Vps_PageCollection_Exception('Parent Page does not exist.');
        }

        if ($id == $rootId) {
          throw new Vps_PageCollection_Exception('Cannot set Parent for Root Page.');
        }

        $this->_pageParentIds[$id] = $parentId;
    }

    public function getPageByPath($path)
    {
        $ids = $this->getIdsForPath($path);
        $page = $this->getPageById(array_pop($ids));
        $this->_currentPage = $page;
        return $page;
    }

    public function getIdsForPath($path)
    {
        $ids = array();
        $matches = array();
        if ($this->_urlScheme == Vps_PageCollection_Abstract::URL_SCHEME_FLAT) {
            if (preg_match('/^\/[a-z0-9]+_[a-z0-9]+_([0-9\_\.]+)\.html?$/', $path, $matches)) {
                if (isset($matches[2]) && $matches[2] != '') {
                    $ids = $this->getIdsForPath('/' . $matches[2], $this->getPageById($matches[1]));
                } else {
                    $ids[] = $matches[1];
                }
            } else {
                $ids[] = $this->getRootPage()->getId();
            }
        } else if ($this->_urlScheme == Vps_PageCollection_Abstract::URL_SCHEME_HIERARCHICAL) {
            if (preg_match('/^(\/\w+)*\/$/', $path)) { // hierarchische URLs, Format /x/y/z/
                $page = $this->getRootPage();
                $ids[] = $page->getId();
                $pathParts = explode('/', substr($path, 1, -1));
                foreach($pathParts as $pathPart) {
                    if ($pathPart != '') {
                        $page = $this->getChildPage($page, $pathPart);
                        if (!$page) {
                            return array();
                        } else {
                            $ids[] = $page->getId();
                        }
                    }
                }
            }
        }

        return $ids;
    }

    public function getParentPage(Vpc_Interface $page)
    {
        $id = $page->getId();
        if (isset($this->_pageParentIds[$id])) {
            $parentId = $this->_pageParentIds[$id];
            if (!isset($this->_pages[$parentId])) {
                unset($this->_pageParentIds[$id]);
                return $this->getParentPage($page);
            } else {
                return $this->_pages[$parentId];
            }
        } else if ($id != $this->getRootPage()->getId()) {
            $data = $this->_dao->getTable('Vps_Dao_Pages')->retrieveParentPageData($id);
            if (!empty($data)) {
                if ($data['component_id'] == $this->getRootPage()->getId()) {
                    return $this->getRootPage();
                } else {
                    $component = new $data['component']($this->_dao, $data['component_id']);
                    $parentPage = $this->addPage($component, $data['filename']);
                    $this->setParentPage($page, $parentPage);
                    return $parentPage;
                }
            }
            return null;
        } else { // ParentPage von RootPage
            return null;
        }
    }

    public function getChildPages(Vpc_Interface $page)
    {
        $page->generateHierarchy();
        $childs = array();
        $searchId = $page->getId();
        foreach($this->_pageParentIds as $id=>$parentId) {
            if($parentId == $searchId) {
                $childs[] = $this->_pages[$id];
            }
        }
        return $childs;
    }

    public function getChildPage(Vpc_Interface $page, $filename)
    {
        $page->generateHierarchy($filename);
        $searchId = $page->getId();
        foreach($this->_pageParentIds as $id => $parentId) {
            if($parentId == $searchId && $filename == $this->_pageFilenames[$id]) {
                return $this->_pages[$id];
            }
        }
        return null;
    }
    
    public function getPath($page)
    {
        $pageId = $page->getId();
        $rootId = $this->getRootPage()->getId();

        $path = '/';
        if ($this->_urlScheme == Vps_PageCollection_Abstract::URL_SCHEME_HIERARCHICAL) {
            while ($pageId != $rootId) {
                $path = '/' . $this->_pageFilenames[$pageId] . $path;
                $page = $this->getParentPage($page);
                $pageId = $page ? $page->getId() : $rootId;
            }
        } else {
            if ($pageId != $rootId) {
                $path .= 'de_' . $this->_pageFilenames[$pageId] . '_' . $pageId . '.html';
            }
        }

        return $path;
    }
    
    public function getTitle($page)
    {
        $title = array();
        while ($page) {
            $data = $this->getPageData($page);
            $title[] = $data['name'];
            $page = $this->getParentPage($page);
        }
        return implode(' - ', $title);
    }
    
}
