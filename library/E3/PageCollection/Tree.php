<?php
class E3_PageCollection_Tree extends E3_PageCollection_Abstract
{
    protected $_pageParentIds = array();
    

    public function setParentPage(E3_Component_Abstract $page, E3_Component_Abstract $parentPage)
    {
        $id = $page->getComponentId();
        $parentId = $parentPage->getComponentId();
        $rootId = $this->getRootPage()->getComponentId();
        
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
            $childPages = $this->getChildPages($page);
            $found = false;
            foreach($childPages as $p) {
                if($this->_pageFilenames[$p->getComponentId()]==$pathPart) {
                    $page = $p;
                    $found = true;
                    break;
                }
            }
            if(!$found) return null;
        }
        return $page;
    }
    
    public function getParentPage(E3_Component_Abstract $page)
    {
       	$return = null;
        $id = $page->getComponentId();
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

    public function getChildPages(E3_Component_Abstract $page)
    {
        $page->callGenerateHierarchy($this);
        $childs = array();
        $searchId = $page->getComponentId();
        foreach($this->_pageParentIds as $id=>$parentId) {
            if($parentId == $searchId) {
                $childs[] = $this->_pages[$id];
            }
        }
        return $childs;
    }
}
