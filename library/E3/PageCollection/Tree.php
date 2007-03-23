<?php
class E3_PageCollection_Tree extends E3_PageCollection_Abstract
{
    protected $_pageParentIds = array();
    

    public function setParentPage(E3_Component_Interface $page, E3_Component_Interface $parentPage)
    {
        $id = $page->getId();
        $parentId = $parentPage->getId();
        $rootId = $this->getRootPage()->getId();
        
        if ($parentId == $id) {
        	throw new E3_PageCollection_Exception('Cannot set Parent Page for the same object.');
        }
        
        if (!isset($this->_pages[$id])) {
        	throw new E3_PageCollection_Exception('Page does not exist.');
        }
        
        if (!isset($this->_pages[$parentId]) && $rootId != $parentId) {
        	throw new E3_PageCollection_Exception('Parent Page does not exist.');
        }
        
        if ($id == $rootId) {
        	throw new E3_PageCollection_Exception('Cannot set Parent for Root Page.');
        }
        
        $this->_pageParentIds[$id] = $parentId;
    }

    public function getPageByPath($path)
    {
        $path = trim($path, '/');
        if($path=='') return $this->getRootPage(); //home

        $pathParts = explode('/', $path);
        $page = $this->getRootPage();
        foreach($pathParts as $pathPart) {
            $page = $this->getChildPage($page, $pathPart);
            if(!$page) return null;
        }
        return $page;
    }
    
    public function getParentPage(E3_Component_Interface $page)
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

    public function getChildPages(E3_Component_Interface $page)
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

    public function getChildPage(E3_Component_Interface $page, $filename)
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
}
