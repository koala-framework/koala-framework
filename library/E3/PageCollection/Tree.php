<?php
class E3_PageCollection_Tree extends E3_PageCollection_Abstract
{
    protected $_pageParentIds = array();

    public function setParentPage($page, $parentPage)
    {
        $this->_pageParentIds[$page->getComponentId()] = $parentPage->getComponentId();
    }

    public function getPageByPath($path)
    {
        $pathParts = explode('/', trim($path, '/'));
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
    
    public function getParentPage($page)
    {
        $parentId = $this->_pageParentIds[$page->getComponentId()];
        return $this->_pages[$parentId];
    }

    public function getChildPages($page)
    {
        $page->generateHierachy($this);
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
