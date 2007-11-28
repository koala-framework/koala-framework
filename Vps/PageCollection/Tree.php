<?p
class Vps_PageCollection_Tree extends Vps_PageCollection_Abstra

    protected $_pageParentIds = array(

    protected function _removePage($i
   
        if (isset($this->_pageParentIds[$id]))
            unset($this->_pageParentIds[$id]
       
        return parent::_removePage($id
   

    public function addTreePage($page, $filename = '', $name = '', $parentPage = nul
   
        $page = parent::addPage($page, $filename, $name
        $this->setParentPage($page, $parentPage
        return $pag
   

    public function setParentPage(Vpc_Interface $page, Vpc_Interface $parentPage = nul
   
        $id = $page->getPageId(
        if (is_null($parentPage))
            $parentId = nul
        } else
            $parentId = $parentPage->getPageId(

            if ($parentId == $id)
                d($page->getId()
                throw new Vps_PageCollection_Exception('Cannot set Parent Page for the same object: ' . $id
           

            if (!isset($this->_pages[$parentId]))
                throw new Vps_PageCollection_Exception('Parent Page does not exist: ' . $parentId
           

            if (!isset($this->_pages[$id]))
                throw new Vps_PageCollection_Exception('Page does not exist: ' . $id
           
       

        $this->_pageParentIds[$id] = $parentI
        // TODO: abchecken, ob es filename nicht doppelt gibt auf aktueller ebe
   

    public function findPageByPath($pat
   
        $ids = $this->getIdsForPath($path
        $page = $this->findPage(array_pop($ids)
        $this->_currentPage = $pag
        return $pag
   

    public function getParentPage(Vpc_Interface $pag
   
        $id = $page->getPageId(
        if (isset($this->_pageParentIds[$id])) { // Page gibt es und es ist eine ParentId geset
            $parentId = $this->_pageParentIds[$id
            if (isset($this->_pages[$parentId]))
                return $this->_pages[$parentId
           
        } else { // Page gibt es nicht, wird erstel
            $data = $this->_dao->getTable('Vps_Dao_Pages')->retrieveParentPageData($id
            if ($data)
                $parentPage = $this->findPage($data['id']
                $this->setParentPage($page, $parentPage
                return $parentPag
           
       
        return nul
   

    public function getChildPages(Vpc_Interface $page = null, $type = nul
   
        $this->_generateHierarchy($page, ''
        $searchId = $page ? $page->getPageId() : null
        $childPages = array(
        foreach ($this->_pageParentIds as $id => $parentId)
            if ($type && !$page)
                if ($parentId == $searchId 
                    isset($this->_types[$id]) 
                    $this->_types[$id] == $ty
                )
                    $childPages[] = $this->_pages[$id
               
            } else
                if ($parentId == $searchId)
                    $childPages[] = $this->_pages[$id
               
           
       
        return $childPage
   

    public function getChildPage(Vpc_Interface $page = null, $filename = '
   
        $this->_generateHierarchy($page, $filename
        $searchId = $page ? $page->getPageId() : nul
        // Nach gleichem Filename such
        foreach ($this->_pageParentIds as $id => $parentId)
            if($parentId == $searchId && $filename == $this->_pageFilenames[$id])
                return $this->_pages[$id
           
       
        // Wenn nicht mit gleichem Filename gefunden, erste Unterseite liefe
        $id = array_search($searchId, $this->_pageParentIds
        if ($id)
            return $this->findPage($id
       
        return nul
   

    public function findComponentByClass($class, Vpc_Interface $startPage = nul
   
        $rowset = $this->_dao->getTable('Vps_Dao_Pages')->fetchAll("component_class = '$class'"
        if ($rowset->count() > 0)
            $startPage = $this->findPage($rowset->current()->id
       

        $table = $this->_dao->getTable('Vpc_Paragraphs_Model
                                            //unrichtig, aber im prinzip egal da nur in der datenbank geschaut wi
                    array('componentClass'=>'Vpc_Paragraphs_Component')
        $rowset = $table->fetchAll("component_class = '$class'"
        if ($rowset->count() > 0)
            $startPage = $this->findPage($rowset->current()->page_id
       

        if ($startPage)
            $component = $startPage->findComponentByClass($class
            if ($component)
                return $componen
           
       

        foreach ($this->getChildPages($startPage) as $page)
            $component = $this->findComponentByClass($class, $page
            if ($component != null)
                return $componen
           
       

        return nul
   

    public function getTitle($pag
   
        $title = array(
        while ($page)
            $title[] = parent::getTitle($page
            $page = $this->getParentPage($page
       
        return implode(' - ', $title
   

    // ********** URL-abhängige Methoden *********
    public function getIdsForPath($pat
   
        $ids = array(
        $matches = array(
        if ($this->_urlScheme == Vps_PageCollection_Abstract::URL_SCHEME_FLAT)
            $pattern = '/^\/.*?_(' . Vpc_Abstract::getIdPattern() . ')\.html$/
            if (preg_match($pattern, $path, $matches))
                $ids[] = $matches[1
           
        } else if ($this->_urlScheme == Vps_PageCollection_Abstract::URL_SCHEME_HIERARCHICAL)
            if (preg_match('/^(\/\w+)*$/', $path)) { // hierarchische URLs, Format /x/y/
                $page = nul
                $pathParts = explode('/', substr($path, 1)
                foreach($pathParts as $key => $pathPart)
                    if ($pathPart != '')
                        try
                            $page = $this->getChildPage($page, $pathPart
                        } catch (Vpc_UrlNotFoundException $e)
                            $newPath = '
                            for ($x = 0; $x < $key; $x++)
                                $newPath .= '/' . $pathParts[$x
                           
                            $newPath .= '/' . $e->getMessage(
                            header('Location: ' . $newPath, true, 301
                            die(
                       
                        if (!$page)
                            return array(
                        } else
                            $ids[] = $page->getPageId(
                       
                   
               
           
       
        return $id
   

    public function getUrl($pag
   
        // Erste Nicht-Decorator-Komponente raussuch
        $p = $pag
        while ($p instanceof Vpc_Decorator_Abstract)
            $p = $p->getChildComponent(
       
      
        if ($p instanceof Vpc_Basic_LinkTag_Component)
            $templateVars = $p->getTemplateVars(
            $url = $templateVars['href'
            if ($templateVars['param'] != '')
                $url .= '?' . $templateVars['param'
           
            return $ur
        } else
            $id = $page->getPageId(
  
            $path = '/
            if ($this->getHomePage()->getPageId() == $id)
                return $pat
           
            if ($this->_urlScheme == Vps_PageCollection_Abstract::URL_SCHEME_HIERARCHICAL)
                while ($id)
                    $path = '/' . $this->_pageFilenames[$id] . $pat
                    $page = $this->getParentPage($page
                    $id = $page ? $page->getPageId() : nul
               
                if (strlen($path) > 1 && substr($path, -1) == '/')
                    $path = substr($path, 0, -1
               
            } else
                if (isset($this->_pageFilenames[$id]))
                    $path .= 'de_' . $this->_pageFilenames[$id] . '_' . $id . '.html
               
           
  
            return $pat
       
   

    public function createUrl($parentPage, $filename, $pageKeySuffix = '', $pageTagSuffix = '
   
        $page = $parentPage->createPage('Vpc_Empty', $pageKeySuffix, $pageTagSuffix
        $page = $this->addTreePage($page, $filename, $filename, $parentPage
        return $this->getUrl($page
   
  

