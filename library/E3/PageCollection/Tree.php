<?php
class E3_PageCollection_Tree extends E3_PageCollection_Abstract
{
    protected $_pageParentIds = array();
    

    public function setParentPage(E3_Component_Abstract $page, E3_Component_Abstract $parentPage)
    {
        $this->_pageParentIds[$page->getComponentId()] = $parentPage->getComponentId();
    }

    public function getPageByPath($path)
    {
        $path = trim($path, '/');
        if($path=='') return $this->_rootPage; //home

        $pathParts = explode('/', $path);
        $page = $this->_rootPage;
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
        $parentId = $this->_pageParentIds[$page->getComponentId()];
        return $this->_pages[$parentId];
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
