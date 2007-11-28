<?p
abstract class Vps_PageCollection_Abstra

    protected $_pageFilenames = array(
    protected $_pageNames = array(
    protected $_pages = array(
    protected $_hideInMenu = array(
    protected $_homeI
    protected $_decoratorClasses = array(
    protected $_da
    protected static $_instance = nul
    private $_createDynamicPages = tru
    protected $_pageData = array(
    protected $_currentPage = nul
    protected $_urlScheme = 
    const URL_SCHEME_HIERARCHICAL = 
    const URL_SCHEME_FLAT = 
    private $_showInvisible = fals
    protected $_types = array(

    public function __construct(Vps_Dao $dao, $urlScheme = Vps_PageCollection_Abstract::URL_SCHEME_HIERARCHICAL, $decoratorClasses = array(
   
        $this->_dao = $da
        switch ($urlScheme)
            case Vps_PageCollection_Abstract::URL_SCHEME_HIERARCHICA
            case Vps_PageCollection_Abstract::URL_SCHEME_FLA
                $this->_urlScheme = $urlSchem
                brea
            defaul
                throw new Vps_PageCollection_Exception('Invalid urlScheme specified'
       
        $this->_decoratorClasses = $decoratorClasse
   

    public function getDao
   
        return $this->_da
   

    public function showInvisible($show = nul
   
        if ($show === true || $show === false)
            $this->_showInvisible = $sho
            $this->_dao->getTable('Vps_Dao_Pages')->showInvisible($show
       
        return $this->_showInvisibl
   

    public static function getInstance
   
        if (null === self::$_instance)
            $dao = Zend_Registry::get('dao'

            $pageCollectionConfig = new Zend_Config_Ini('application/config.ini', 'pagecollection'
            if ($pageCollectionConfig->pagecollection->urlscheme == 'flat')
                $urlScheme = Vps_PageCollection_Abstract::URL_SCHEME_FLA
            } else
                $urlScheme = Vps_PageCollection_Abstract::URL_SCHEME_HIERARCHICA
           
            $decoratorClasses = $pageCollectionConfig->pagecollection->addDecorators->toArray(
            $pageCollection = new $pageCollectionConfig->pagecollection->type($dao, $urlScheme, $decoratorClasses
            self::$_instance = $pageCollectio
       

        return self::$_instanc
   

    public function addPage($page, $filename = '', $name = '
   
        if (!$page instanceof Vpc_Interface && (int)$page > 0)
            $id = (int)$pag
            $pageData = $this->_dao->getTable('Vps_Dao_Pages')->retrievePageData($id
            $page = Vpc_Abstract::createInstance($this->_dao, $pageData['component_class'], $id, $this
            if ($page)
                $filename = $pageData['filename'
                $name = $pageData['name'
            } else
                throw new Vps_Page_Collection_Exception("Couldn\'t create Page with id $id"
           
       
        if (!$page instanceof Vpc_Interface)
            throw new Vps_PageCollection_Exception("Component must be instance of Vpc_Interface."
       

        if ($filename == '')
            throw new Vps_PageCollection_Exception("Pagename must not be empty. Probably Component is not a Page."
       

        $page->setPageCollection($this
        $id = $page->getPageId(
        if (isset($this->_pages[$id]))
            $decoratedComponent = $this->_removePage($id
        } else
            $decoratedComponent = $this->_addDecorators($page
       

        $this->_setPage($decoratedComponent, $filename, $name
        if (Zend_Registry::isRegistered('infolog'))
            Zend_Registry::get('infolog')->createPage($decoratedComponent->getId()
       
        return $decoratedComponen
   

    protected function _removePage($i
   
        $page = nul
        if (isset($this->_pages[$id]))
            $page = $this->_pages[$id
            unset($this->_pages[$id]
            unset($this->_pageFilenames[$id]
       
        return $pag
   

    public function hideInMenu(Vpc_Interface $pag
   
        $this->_hideInMenu[] = $page->getPageId(
   

    protected function _addDecorators(Vpc_Interface $pag
   
        foreach ($this->_decoratorClasses as $class)
            try
                if (class_exists($class))
                    $page = new $class($this->_dao, $page
                    $page->setPageCollection($this
               
            } catch (Zend_Exception $e)
                throw new Vpc_ComponentNotFoundException("Decorator '$class' not found."
           
       
        return $pag
   

    private function _setPage(Vpc_Interface $page, $filename, $nam
   
        $id = $page->getPageId(

        if (isset($this->_pages[$id]))
            throw new Vps_PageCollection_Exception('A page with the same componentId already exists.'
       

        $this->_pages[$id] = $pag
        $this->_pageFilenames[$id] = Zend_Filter::get($filename, 'Url', array(), 'Vps_Filter')
        $this->_pageNames[$id] = $name
        if (!$name) 
            $this->hideInMenu($page)
       
   

    public function findPage($i
   
        if (is_null($id))
            return $this->getHomePage(
       

        try
            $parts = Vpc_Abstract::parseId($id
        } catch (Vpc_Exception $e)
            return nul
       
        $id = $parts['pageId'
        if (!isset($this->_pages[$id]))
            $currentId = $parts['dbId'
            $page = $this->addPage($currentId
            if ($page != null)
                foreach ($parts['pageKeys'] as $currentPageKey => $pageKey)
                    $this->_pages[$currentId]->generateHierarchy($pageKey
                    $currentId = $parts['dbId'] . $currentPageKe
               
           
       
        if (isset($this->_pages[$id]))
            return $this->_pages[$id
        } else
            return nul
       
   

    public function findComponent($i
   
        $page = $this->findPage($id
        if ($page)
            return $page->findComponent($id
       
        return nul
   

    public function getHomePage
   
        if (!isset($this->_homeId))
            $data = $this->_dao->getTable('Vps_Dao_Pages')->retrieveHomePageData(
            $page = $this->findPage($data['id']
            $this->_homeId = $page->getPageId(
       
        return $this->_pages[$this->_homeId
   

    protected function _generateHierarchy(Vpc_Interface $page = null, $filename = '
   
        if (is_null($page))
            $rows = $this->_dao->getTable('Vps_Dao_Pages')->retrieveChildPagesData(null
            foreach($rows as $pageRow)
                $page = Vpc_Abstract::createInstance($this->getDao(), $pageRow['component_class'], $pageRow['id'], $this
                $this->addTreePage($page, $pageRow['filename'], $pageRow['name'], null
                $this->_types[$page->getId()] = $pageRow['type'
           
        } else
            $page->generateHierarchy($filename
       
   

    public function getCurrentPage
   
        return $this->_currentPag
   

    public function getUrl($pag
   
        return '
   
  
    public function createUrl
   
        return '
   

    public function getName($pag
   
        $id = $page->getPageId(
        if (isset($this->_pageNames[$id]))
            return $this->_pageNames[$id
        } else
            return '
       
   

    public function getPageData(Vpc_Interface $pag
   
        $id = $page->getPageId(
        $data = $this->_dao->getTable('Vps_Dao_Pages')->retrievePageData($id, false
        $data['url'] = $this->getUrl($page
        $data['name'] = $this->_pageNames[$page->getPageId()
        if (array_search($page->getPageId(), $this->_hideInMenu) !== false 
            isset($data['hide']) && $data['hide'] ==
        )
            $data['hide'] = tru
        } else
            $data['hide'] = fals
       
      
        // Erste Nicht-Decorator-Komponente raussuch
        $p = $pag
        while ($p instanceof Vpc_Decorator_Abstract)
            $p = $p->getChildComponent(
       

        if ($p instanceof Vpc_Basic_LinkTag_Component)
            $templateVars = $p->getTemplateVars(
            $data['rel'] = $templateVars['rel'
        } else
            $data['rel'] = '
       

        return $dat
   

    public function getFilename(Vpc_Interface $pag
   
        return isset($this->_pageFilenames[$page->getPageId()]) ? $this->_pageFilenames[$page->getPageId()] : '
   

    abstract public function findPageByPath($path

    public function findPageByFilename($filename
    
        $id = array_search($filename, $this->_pageFilenames)
        if ($id !== false) 
            return $this->findPage($id)
        
        return null
    
   
    public function findComponentByClass($class, $startPage = nul
   
        $ids = $this->_dao->getTable('Vps_Dao_Pages')->findPagesByClass($class
        $id = (int)array_shift($ids
        if ($id > 0)
            $page = $this->findPage($id
            if ($page)
                $component = $page->findComponentByClass($class
                if ($component)
                    return $componen
               
           
       
        return nul
   

    public function getTitle($pag
   
        $data = $this->getPageData($page
        return isset($data['name']) ? $data['name'] : '
   

