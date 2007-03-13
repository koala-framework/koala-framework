<?php
class E3_PageCollection_Tree extends E3_PageCollection_Abstract
{
    protected $_pageParentIds;

    public function createPage($id, $filename, $component, $parentPage)
    {
        $page = parent::createPage($id, $filename, $component, $parentPage);
        if($parentPage) {
            $this->_pageParentIds[$id] = $parentPage->getPageId();
        }
    }

    public function getPageByPath($path)
    {
        $pathParts = explode('/', trim($path, '/'));
        $page = $this->getPageById(0);
        foreach($pathParts as $pathPart) {
            $childPages = $this->getChildPages($page);
            $found = false;
            foreach($childPages as $p) {
                if($this->_pageFilenames[$p->getPageId()]==$pathPart) {
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
        return $this->getPageById($this->_pageParentIds[$page->getPageId()]);
    }

    public function getChildPages($page)
    {
        $page->generateHierachy();
        $childs = array();
        foreach($this->_pageParentIds as $id=>$parentId) {
            if($parentId == $page->getPageId()) {
                $childs[] = $this->getPageById($id);
            }
        }
        return $childs;
    }
}
