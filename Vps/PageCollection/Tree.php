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
        // TODO: abchecken, ob es filename nicht doppelt gibt auf aktueller ebene
    }

    public function getPageByPath($path)
    {
        $ids = $this->getIdsForPath($path);
        if ($ids == array()) return null;
        $page = $this->getPageById(array_pop($ids));
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
                $parentPage = $this->getPageById($data['id']);
                $this->setParentPage($page, $parentPage);
                return $parentPage;
            }
        }
        return null;
    }

    public function getChildPages(Vpc_Interface $page = null, $type = null)
    {
        if (is_null($page)) {
            $ret = array();
            $rows = $this->_dao->getTable('Vps_Dao_Pages')->retrieveChildPagesData(null);
            foreach ($rows as $pageRow) {
                if (!$type || $pageRow['type'] == $type) {
                    if (!$page = $this->getExistingPageById($pageRow['id'])) {
                        $page = Vpc_Abstract::createInstance($this->getDao(), $pageRow['component_class'], $pageRow['id'], $this);
                        $this->addTreePage($page, $pageRow['filename'], $pageRow['name'], null);
                        $this->_types[$page->getId()] = $pageRow['type'];
                    }
                    $ret[] = $page;
                }
            }
            return $ret;
        } else {
            return $page->getPageFactory()->getChildPages();
        }
    }

    public function getMenuChildPages(Vpc_Interface $page = null, $type = null)
    {
        if (is_null($page)) {
            return $this->getChildPages($page, $type);
        } else {
            return $page->getPageFactory()->getMenuChildPages();
        }
    }

    public function getChildPageByFilename(Vpc_Interface $page = null, $filename)
    {
        if (is_null($page)) {
            return $this->getExistingChildPageByFilename(null, $filename);
        }
        return $page->getPageFactory()->getChildPageByFilename($filename);
    }

    //wird verwendet von Vpc_Abstract um bestehende Pages nicht nochmal zu erstellen
    public function getExistingChildPageByFilename(Vpc_Interface $page=null, $filename)
    {
        if (is_null($page)) {
            //root erstellen
            $this->getChildPages(null);
        }
        $searchId = !is_null($page) ? $page->getPageId() : null;
        foreach ($this->_pageParentIds as $id => $parentId) {
            if ($parentId == $searchId && $this->_pageFilenames[$id] == $filename) {
                return $this->_pages[$id];
            }
        }
        return null;
    }

    //wird verwendet von Vpc_Abstract um bestehende Pages nicht nochmal zu erstellen
    public function getExistingPageById($id)
    {
        if (isset($this->_pages[$id])) {
            return $this->_pages[$id];
        }
    }

    public function getComponentByClass($class, Vpc_Interface $startPage = null)
    {
        $rowset = $this->_dao->getTable('Vps_Dao_Pages')->fetchAll("component_class = '$class'");
        if ($rowset->count() > 0) {
            $startPage = $this->getPageById($rowset->current()->id);
        }

        $table = $this->_dao->getTable('Vpc_Paragraphs_Model',
                                            //unrichtig, aber im prinzip egal da nur in der datenbank geschaut wird
                    array('componentClass'=>'Vpc_Paragraphs_Component'));
        $rowset = $table->fetchAll("component_class = '$class'");
        if ($rowset->count() > 0) {
            $startPage = $this->getPageById($rowset->current()->component_id);
        }

        if ($startPage) {
            $component = $startPage->getComponentByClass($class);
            if ($component) {
                return $component;
            }
        }

        foreach ($this->getChildPages($startPage) as $page) {
            $component = $this->getComponentByClass($class, $page);
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
        if ($path == '/') {
            $ids[] = $this->getHomePage()->getPageId();
            return $ids;
        }
        $matches = array();
        if ($this->_urlScheme == Vps_PageCollection_Abstract::URL_SCHEME_FLAT) {
            $pattern = '/^\/.*?_(' . Vpc_Abstract::getIdPattern() . ')\.html$/';
            if (preg_match($pattern, $path, $matches)) {
                $ids[] = $matches[1];
            }
        } else if ($this->_urlScheme == Vps_PageCollection_Abstract::URL_SCHEME_HIERARCHICAL) {
            if (preg_match('/^(\/\w+)*$/', $path)) { // hierarchische URLs, Format /x/y/z/
                $page = null;
                $pathParts = explode('/', substr($path, 1));
                foreach ($pathParts as $key => $pathPart) {
                    if ($pathPart != '') {
                        try {
                            $page = $this->getChildPageByFilename($page, $pathPart);
                        } catch (Vpc_UrlNotFoundException $e) {
                            $newPath = '';
                            for ($x = 0; $x < $key; $x++) {
                                $newPath .= '/' . $pathParts[$x];
                            }
                            $newPath .= '/' . $e->getMessage();
                            header('Location: ' . $newPath, true, 301);
                            die();
                        }
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

        if ($p instanceof Vpc_Basic_LinkTag_Component ||
            $p instanceof Vpc_Basic_Link_Component_Component)
        {
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
                if (strlen($path) > 1 && substr($path, -1) == '/') {
                    $path = substr($path, 0, -1);
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
