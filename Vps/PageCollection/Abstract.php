<?php
abstract class Vps_PageCollection_Abstract
{
    protected $_pageFilenames = array();
    protected $_pages = array();
    protected $_rootPageId;
    protected $_addDecorator = false;
    protected $_dao;
    protected static $_instance = null;
    private $_createDynamicPages = true;
    protected $_pageData = array();
    protected $_currentPage = null;
    protected $_urlScheme = 0;
    const URL_SCHEME_HIERARCHICAL = 0;
    const URL_SCHEME_FLAT = 1;

    function __construct(Vps_Dao $dao, $urlScheme = Vps_PageCollection_Abstract::URL_SCHEME_HIERARCHICAL)
    {
        $this->_dao = $dao;
        switch ($urlScheme) {
            case Vps_PageCollection_Abstract::URL_SCHEME_HIERARCHICAL:
            case Vps_PageCollection_Abstract::URL_SCHEME_FLAT:
                $this->_urlScheme = $urlScheme;
                break;
            default:
                throw new Vps_PageCollection_Exception('Invalid urlScheme specified');
        }
    }
    
    public function getDao()
    {
        return $this->_dao;
    }
    
    public static function getInstance()
    {
        if (null === self::$_instance) {
            $dao = Zend_Registry::get('dao');
            
            $pageCollectionConfig = new Zend_Config_Ini('application/config.ini', 'pagecollection');
            if ($pageCollectionConfig->pagecollection->urlscheme == 'flat') {
                $urlScheme = Vps_PageCollection_Abstract::URL_SCHEME_FLAT;
            } else {
                $urlScheme = Vps_PageCollection_Abstract::URL_SCHEME_HIERARCHICAL;
            }
            $pageCollection = new $pageCollectionConfig->pagecollection->type($dao, $urlScheme);
            $pageCollection->setAddDecorator($pageCollectionConfig->pagecollection->addDecorator);
            
            self::$_instance = $pageCollection;
        }

        return self::$_instance;
    }

    public function addPage($page, $filename = '')
    {
        if (is_int($page)) {
            $componentId = $page;
            $pageData = $this->_dao->getTable('Vps_Dao_Pages')->retrievePageData($componentId);
            $page = Vpc_Abstract::createInstance($this->_dao, $componentId);
            if ($page) {
                $page->setPageCollection($this);
                $filename = $pageData['filename'];
            } else {
                throw new Vps_Page_Collection_Exception("Couldn\'t create Component with id $componentId");
            }
        }

        if (!$page instanceof Vpc_Interface) {
            throw new Vps_PageCollection_Exception("Component must be instance of Vpc_Interface.");
        }
        
        if ($filename == '') {
            throw new Vps_PageCollection_Exception("Pagename must not be empty. Probably Component is not a Page.");
        }
        
        $id = $page->getPageId();
        if (isset($this->_pages[$id])) {
            $decoratedComponent = $this->_removePage($id);
        } else {
            $decoratedComponent = $this->addDecoratorsToComponent($page);
        }

        $this->_setPage($decoratedComponent, $filename);
        return $decoratedComponent;
    }
    
    protected function _removePage($id)
    {
        $page = null;
        if (isset($this->_pages[$id])) {
            $page = $this->_pages[$id];
            unset($this->_pages[$id]);
            unset($this->_pageFilenames[$id]);
        }
        return $page;
    }

    public function setAddDecorator($decorator)
    {
        //todo: raise exception if no string, or class does'nt exist, or class doesn't inherit Vpc_Decorator_Abstract
        $this->_addDecorator = $decorator;
    }

    protected function addDecoratorsToComponent(Vpc_Interface $component)
    {
        if ($this->_addDecorator) {
            $component = new $this->_addDecorator($this->_dao, $component);
            $component->setPageCollection($this);
        }
        return $component;
    }

    private function _setPage(Vpc_Interface $page, $filename)
    {
        $id = $page->getPageId();

        if (isset($this->_pages[$id])) {
            throw new Vps_PageCollection_Exception('A page with the same componentId already exists.');
        }

        $this->_pages[$id] = $page;
        $this->_pageFilenames[$id] = $filename;
    }

    public function setRootPage(Vpc_Interface $component)
    {
        $this->_setPage($this->addDecoratorsToComponent($component), '');
        $this->_rootPageId = $component->getId();
    }

    public function getPageById($pageId)
    {
        $this->getRootPage(); // Muss hier gemacht werden
        if (!isset($this->_pages[$pageId])) {
            try {
                $parts = Vpc_Abstract::parsePageId($pageId);
                $page = $this->addPage($parts['topComponentId']);
                if ($page != null) {
                    $id = $page->getPageId();
                    foreach ($parts['pageKeys'] as $pageKey) {
                        $this->_pages[$id]->generateHierarchy($pageKey);
                        $id .= $id == $page->getPageId() ? '_' : '.';
                        $id .= $pageKey;
                    }
                }
            } catch (Vpc_Exception $e) {
                return null;
            }
        }

        if (isset($this->_pages[$pageId])) {
            return $this->_pages[$pageId];
        } else {
            return null;
        }
    }

    public function getRootPage()
    {
        if (!isset($this->_rootPageId)) {
            $data = $this->_dao->getTable('Vps_Dao_Pages')->retrieveRootPageData();
            $rootPage = Vpc_Abstract::createInstance($this->_dao, $data['component_id']);
            $rootPage->setPageCollection($this);
            $this->setRootPage($rootPage);
        }
        return $this->_pages[$this->_rootPageId];
    }
    
    public function setCreateDynamicPages($create)
    {
        if (!is_bool($create)) {
            throw new Vps_PageCollection_Exception('$create must be boolean.');
        }
        $this->_createDynamicPages = $create;
    }
    
    public function getCreateDynamicPages()
    {
        return $this->_createDynamicPages;
    }
    
    public function getCurrentPage()
    {
        return $this->_currentPage;
    }
    
    public function getPath($page)
    {
        return '';
    }

    public function getPageData(Vpc_Interface $page)
    {
        $pageId = $page->getPageId();
        $rootId = $this->getRootPage()->getPageId();
        $data = $this->_dao->getTable('Vps_Dao_Pages')->retrievePageData($pageId);
        $data['path'] = $this->getPath($page);
        return $data;
    }
    
    public function getFilename(Vpc_Interface $page)
    {
        return isset($this->_pageFilenames[$page->getPageId()]) ? $this->_pageFilenames[$page->getPageId()] : '';
    }
    
    abstract public function getPageByPath($path);
}
