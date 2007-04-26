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
    
    public static function getInstance()
    {
        if (null === self::$_instance) {
            $dao = Zend_Registry::get('dao');
            
            $pageCollectionConfig = new Zend_Config_Ini('../application/config.ini', 'pagecollection');
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

    public function addPage(Vps_Component_Abstract $component, $filename)
    {
        if ($filename == '') {
            throw new Vps_PageCollection_Exception("Pagename must not be empty.");
        }
        
        $id = $component->getId();
        if (isset($this->_pages[$id])) {
            $decoratedComponent = $this->_removePage($id);
        } else {
            $decoratedComponent = $this->addDecoratorsToComponent($component);
        }
        
        $this->_setPage($decoratedComponent, $filename);
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
        //todo: raise exception if no string, or class does'nt exist, or class doesn't inherit Vps_Component_Decorator_Abstract
        $this->_addDecorator = $decorator;
    }

    protected function addDecoratorsToComponent(Vps_Component_Abstract $component)
    {
        if ($this->_addDecorator) {
            return new $this->_addDecorator($this->_dao, $component, $this);
        } else {
            return $component;
        }
    }

    private function _setPage(Vps_Component_Interface $component, $filename)
    {
        $id = (string)$component->getId();

        if (isset($this->_pages[$id])) {
          throw new Vps_PageCollection_Exception('A page with the same componentId already exists.');
        }

        $this->_pages[$id] = $component;
        $this->_pageFilenames[$id] = $filename;
    }

    public function setRootPage(Vps_Component_Interface $component)
    {
        $this->_setPage($this->addDecoratorsToComponent($component), '');
        $this->_rootPageId = $component->getId();
    }

    public function getPageById($id)
    {
        $this->getRootPage(); // Muss hier gemacht werden
        if (!isset($this->_pages[$id])) {
            try {
                $parts = Vps_Component_Abstract::parseId($id);
                $componentId = $parts['componentId'];
                $pageRow = $this->_dao->getPageData($componentId);
                if (!empty($pageRow)) {
                    $className = $pageRow['component'];
                    $component = new $className($this->_dao, $pageRow['component_id']);
                    $component->setPageCollection($this);
                    $this->addPage($component, $pageRow['filename']);
                    $id = $component->getId();
                    foreach ($parts['pageKeys'] as $pageKey) {
                        $this->_pages[$id]->generateHierarchy();
                        $id .= $id == $component->getId() ? '_' : '.';
                        $id .= $pageKey;
                    }
                }
            } catch (Vps_Component_Exception $e) {
                return null;
            }
        }

        if (isset($this->_pages[$id])) {
            return $this->_pages[$id];
        } else {
            return null;
        }
    }

    public function getRootPage()
    {
        if (!isset($this->_rootPageId)) {
            $data = $this->_dao->getRootPageData();
            $classname = $data['component'];
            $rootPage = new $classname($this->_dao, $data['component_id']);
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
    
    abstract public function getPageByPath($path);
}
