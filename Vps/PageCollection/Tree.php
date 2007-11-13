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

    public function addTreePage($page, $filename = '', $name = '', $parentPage = null)
    {
        $page = parent::addPage($page, $filename, $name);
        $this->setParentPage($page, $parentPage);
        return $page;
    }

    public function setParentPage(Vpc_Interface $page, Vpc_Interface $parentPage = null)
    {
        $id = $page->getPageId();
        if (is_null($parentPage)) {
            $parentId = null;
        } else {
            $parentId = $parentPage->getPageId();

            if ($parentId == $id) {
                throw new Vps_PageCollection_Exception('Cannot set Parent Page for the same object: ' . $id);
            }

            if (!isset($this->_pages[$parentId])) {
                throw new Vps_PageCollection_Exception('Parent Page does not exist: ' . $parentId);
            }

            if (!isset($this->_pages[$id])) {
                throw new Vps_PageCollection_Exception('Page does not exist: ' . $id);
            }
        }

        $this->_pageParentIds[$id] = $parentId;
    }

    public function findPageByPath($path)
    {
        $ids = $this->getIdsForPath($path);
        $page = $this->findPage(array_pop($ids));
        $this->_currentPage = $page;
        return $page;
    }

    public function getParentPage(Vpc_Interface $page)
    {
        $id = $page->getPageId();
        if (isset($this->_pageParentIds[$id])) { // Page gibt es und es ist eine ParentId gesetzt
            $parentId = $this->_pageParentIds[$id];
            if (isset($this->_pages[$parentId])) {
                return $this->_pages[$parentId];
            }
        } else { // Page gibt es nicht, wird erstellt
            $data = $this->_dao->getTable('Vps_Dao_Pages')->retrieveParentPageData($id);
            if ($data) {
                $parentPage = $this->findPage($data['id']);
                $this->setParentPage($page, $parentPage);
                return $parentPage;
            }
        }
        return null;
    }

    public function getChildPages(Vpc_Interface $page = null, $type = null)
    {
        $this->_generateHierarchy($page, '');
        $searchId = $page ? $page->getPageId() : null ;
        $childPages = array();
        foreach ($this->_pageParentIds as $id => $parentId) {
            if ($type && !$page) {
                if ($parentId == $searchId &&
                    isset($this->_types[$id]) &&
                    $this->_types[$id] == $type
                ) {
                    $childPages[] = $this->_pages[$id];
                }
            } else {
                if ($parentId == $searchId) {
                    $childPages[] = $this->_pages[$id];
                }
            }
        }
        return $childPages;
    }

    public function getChildPage(Vpc_Interface $page = null, $filename = '')
    {
        $this->_generateHierarchy($page, $filename);
        $searchId = $page ? $page->getPageId() : null;
        // Nach gleichem Filename suchen
        foreach ($this->_pageParentIds as $id => $parentId) {
            if($parentId == $searchId && $filename == $this->_pageFilenames[$id]) {
                return $this->_pages[$id];
            }
        }
        // Wenn nicht mit gleichem Filename gefunden, erste Unterseite liefern
        $id = array_search($searchId, $this->_pageParentIds);
        if ($id) {
            return $this->findPage($id);
        }
        return null;
    }

    public function findComponentByClass($class, Vpc_Interface $startPage = null)
    {
        $rowset = $this->_dao->getTable('Vps_Dao_Pages')->fetchAll("component_class = '$class'");
        if ($rowset->count() > 0) {
            $startPage = $this->findPage($rowset->current()->id);
        }
        $rowset = $this->_dao->getTable('Vpc_Paragraphs_Model')->fetchAll("component_class = '$class'");
        if ($rowset->count() > 0) {
            $startPage = $this->findPage($rowset->current()->page_id);
        }
        if (!$startPage) {
            $startPage = $this;
        }
        $component = $startPage->findComponentByClass($class);
        if ($component) {
            return $component;
        }

        foreach ($this->getChildPages($startPage) as $page) {
            $component = $this->findComponentByClass($class, $page);
            if ($component != null) {
                return $component;
            }
        }

        return null;
    }

    public function getTitle($page)
    {
        $title = array();
        while ($page) {
            $title[] = parent::getTitle($page);
            $page = $this->getParentPage($page);
        }
        return implode(' - ', $title);
    }

    // ********** URL-abhängige Methoden ***********
    public function getIdsForPath($path)
    {
        $ids = array();
        $matches = array();
        if ($this->_urlScheme == Vps_PageCollection_Abstract::URL_SCHEME_FLAT) {
            $pattern = '/^\/.*?_(' . Vpc_Abstract::getIdPattern() . ')\.html$/';
            if (preg_match($pattern, $path, $matches)) {
                $ids[] = $matches[1];
            }
        } else if ($this->_urlScheme == Vps_PageCollection_Abstract::URL_SCHEME_HIERARCHICAL) {
            if (preg_match('/^(\/\w+)*\/$/', $path)) { // hierarchische URLs, Format /x/y/z/
                $page = null;
                $pathParts = explode('/', substr($path, 1, -1));
                foreach($pathParts as $pathPart) {
                    if ($pathPart != '') {
                        $page = $this->getChildPage($page, $pathPart);
                        if (!$page) {
                            return array();
                        } else {
                            $ids[] = $page->getPageId();
                        }
                    }
                }
            }
        }
        return $ids;
    }

    public function getUrl($page)
    {
        // Erste Nicht-Decorator-Komponente raussuchen
        $p = $page;
        while ($p instanceof Vpc_Decorator_Abstract) {
            $p = $p->getChildComponent();
        }
        
        if ($p instanceof Vpc_Basic_LinkTag_Component && 
            $page->getId() != $p->getId()
        ) {
            $templateVars = $p->getTemplateVars();
            $url = $templateVars['href'];
            if ($templateVars['param'] != '') {
                $url .= '?' . $templateVars['param'];
            }
            return $url;
        } else {
            $id = $page->getPageId();
    
            $path = '/';
            if ($this->getHomePage()->getPageId() == $id) {
                return $path;
            }
            if ($this->_urlScheme == Vps_PageCollection_Abstract::URL_SCHEME_HIERARCHICAL) {
                while ($id) {
                    $path = '/' . $this->_pageFilenames[$id] . $path;
                    $page = $this->getParentPage($page);
                    $id = $page ? $page->getPageId() : null;
                }
            } else {
                if (isset($this->_pageFilenames[$id])) {
                    $path .= 'de_' . $this->_pageFilenames[$id] . '_' . $id . '.html';
                }
            }
    
            return $path;
        }
    }

}
