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

    public function setParentPage(Vps_Component_Interface $page, Vps_Component_Interface $parentPage)
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
        return $this->getPageById(array_pop($ids));
    }

    public function getIdsForPath($path)
    {
        $ids = array();
        $matches = array();
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
        } else if (preg_match('/^\/[a-z0-9]+_[a-z0-9]+_([0-9\_\.]+)$/', $path, $matches)) {
            $ids[] = $matches[1];
        }
        return $ids;
    }

    public function getParentPage(Vps_Component_Interface $page)
    {
        $return = null;
        $id = $page->getId();
        if (isset($this->_pageParentIds[$id])) {
          $parentId = $this->_pageParentIds[$id];
          if (!isset($this->_pages[$parentId])) {
            unset($this->_pageParentIds[$id]);
          } else {
            $return = $this->_pages[$parentId];
          }
        }
        return $return;
    }

    public function getChildPages(Vps_Component_Interface $page)
    {
        $page->generateHierarchy($this);
        $childs = array();
        $searchId = $page->getId();
        foreach($this->_pageParentIds as $id=>$parentId) {
            if($parentId == $searchId) {
                $childs[] = $this->_pages[$id];
            }
        }
        return $childs;
    }

    public function getChildPage(Vps_Component_Interface $page, $filename)
    {
        $page->generateHierarchy($this, $filename);
        $childs = array();
        $searchId = $page->getId();
        foreach($this->_pageParentIds as $id=>$parentId) {
            if($parentId == $searchId && $filename == $this->_pageFilenames[$id]) {
                return $this->_pages[$id];
            }
        }
        return null;
    }

    public function getPageData(Vps_Component_Interface $page)
    {
        $rootId = $this->getRootPage()->getId();
        $id = $page->getId();
        $data = $this->_dao->getPageData($id);
         
        $pageId = $page->getId();
        $data['path'] = '/';
        while ($pageId != $rootId) {
            $data['path'] .= $this->_pageFilenames[$pageId] . '/';
            $pageId = $this->_pageParentIds[$pageId];
        }
        
        return $data;
    }
}
