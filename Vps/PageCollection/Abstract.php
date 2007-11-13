<?php
abstract class Vps_PageCollection_Abstract
{
    protected $_pageFilenames = array();
    protected $_pageNames = array();
    protected $_pages = array();
    protected $_homeId;
    protected $_decoratorClasses = array();
    protected $_dao;
    protected static $_instance = null;
    private $_createDynamicPages = true;
    protected $_pageData = array();
    protected $_currentPage = null;
    protected $_urlScheme = 0;
    const URL_SCHEME_HIERARCHICAL = 0;
    const URL_SCHEME_FLAT = 1;
    private $_showInvisible = false;
    protected $_types = array();

    public function __construct(Vps_Dao $dao, $urlScheme = Vps_PageCollection_Abstract::URL_SCHEME_HIERARCHICAL, $decoratorClasses = array())
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
        $this->_decoratorClasses = $decoratorClasses;
    }

    public function getDao()
    {
        return $this->_dao;
    }

    public function showInvisible($show = null)
    {
        if ($show === true || $show === false) {
            $this->_showInvisible = $show;
            $this->_dao->getTable('Vps_Dao_Pages')->showInvisible($show);
        }
        return $this->_showInvisible;
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
            $decoratorClasses = $pageCollectionConfig->pagecollection->addDecorators->toArray();
            $pageCollection = new $pageCollectionConfig->pagecollection->type($dao, $urlScheme, $decoratorClasses);
            self::$_instance = $pageCollection;
        }

        return self::$_instance;
    }

    public function addPage($page, $filename = '', $name = '')
    {
        if (!$page instanceof Vpc_Interface && (int)$page > 0) {
            $id = (int)$page;
            $pageData = $this->_dao->getTable('Vps_Dao_Pages')->retrievePageData($id);
            $page = Vpc_Abstract::createInstance($this->_dao, $pageData['component_class'], $id, $this);
            if ($page) {
                $filename = $pageData['filename'];
                $name = $pageData['name'];
            } else {
                throw new Vps_Page_Collection_Exception("Couldn\'t create Page with id $id");
            }
        }
        if (!$page instanceof Vpc_Interface) {
            throw new Vps_PageCollection_Exception("Component must be instance of Vpc_Interface.");
        }

        if ($filename == '' || $name == '') {
            throw new Vps_PageCollection_Exception("Pagename and Name must not be empty. Probably Component is not a Page.");
        }

        $page->setPageCollection($this);
        $id = $page->getPageId();
        if (isset($this->_pages[$id])) {
            $decoratedComponent = $this->_removePage($id);
        } else {
            $decoratedComponent = $this->_addDecorators($page);
        }

        $this->_setPage($decoratedComponent, $filename, $name);
        if (Zend_Registry::isRegistered('infolog')) {
            Zend_Registry::get('infolog')->createPage($decoratedComponent->getId());
        }
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

    protected function _addDecorators(Vpc_Interface $page)
    {
        foreach ($this->_decoratorClasses as $class) {
            try {
                if (class_exists($class)) {
                    $page = new $class($this->_dao, $page);
                    $page->setPageCollection($this);
                }
            } catch (Zend_Exception $e) {
                throw new Vpc_ComponentNotFoundException("Decorator '$class' not found.");
            }
        }
        return $page;
    }

    private function _setPage(Vpc_Interface $page, $filename, $name)
    {
        $id = $page->getPageId();

        if (isset($this->_pages[$id])) {
            throw new Vps_PageCollection_Exception('A page with the same componentId already exists.');
        }

        $this->_pages[$id] = $page;
        $this->_pageFilenames[$id] = $filename;
        $this->_pageNames[$id] = $name;
    }

    public function findPage($id)
    {
        if (is_null($id)) {
            return $this->getHomePage();
        }
        
        try {
            $parts = Vpc_Abstract::parseId($id);
        } catch (Vpc_Exception $e) {
            return null;
        }
        $id = $parts['pageId'];
        if (!isset($this->_pages[$id])) {
            $currentId = $parts['dbId'];
            $page = $this->addPage($currentId);
            if ($page != null) {
                foreach ($parts['pageKeys'] as $currentPageKey => $pageKey) {
                    $this->_pages[$currentId]->generateHierarchy($pageKey);
                    $currentId = $parts['dbId'] . $currentPageKey;
                }
            }
        }
        if (isset($this->_pages[$id])) {
            return $this->_pages[$id];
        } else {
            return null;
        }
    }

    public function findComponent($id)
    {
        $page = $this->findPage($id);
        if ($page) {
            return $page->findComponent($id);
        }
        return null;
    }

    public function getHomePage()
    {
        if (!isset($this->_homeId)) {
            $data = $this->_dao->getTable('Vps_Dao_Pages')->retrieveHomePageData();
            $page = $this->findPage($data['id']);
            $this->_homeId = $page->getPageId();
        }
        return $this->_pages[$this->_homeId];
    }

    protected function _generateHierarchy(Vpc_Interface $page = null, $filename = '')
    {
        if (is_null($page)) {
            $rows = $this->_dao->getTable('Vps_Dao_Pages')->retrieveChildPagesData(null);
            foreach($rows as $pageRow) {
                $page = Vpc_Abstract::createInstance($this->getDao(), $pageRow['component_class'], $pageRow['id'], $this);
                $this->addTreePage($page, $pageRow['filename'], $pageRow['name'], null);
                $this->_types[$page->getId()] = $pageRow['type'];
            }
        } else {
            $page->generateHierarchy($filename);
        }
    }

    public function getCurrentPage()
    {
        return $this->_currentPage;
    }

    public function getUrl($page)
    {
        return '';
    }

    public function getName($page)
    {
        $id = $page->getPageId();
        if (isset($this->_pageNames[$id])) {
            return $this->_pageNames[$id];
        } else {
            return '';
        }
    }

    public function getPageData(Vpc_Interface $page)
    {
        $id = $page->getPageId();
        $data = $this->_dao->getTable('Vps_Dao_Pages')->retrievePageData($id, false);
        $data['url'] = $this->getUrl($page);
        
        // Erste Nicht-Decorator-Komponente raussuchen
        $p = $page;
        while ($p instanceof Vpc_Decorator_Abstract) {
            $p = $p->getChildComponent();
        }
        if ($p instanceof Vpc_Basic_LinkTag_Component) {
            if ($page != $p) {
                $templateVars = $p->getTemplateVars();
                $data['rel'] = $templateVars['rel'];
            }
        } else {
            $data['rel'] = '';
        }
        
        return $data;
    }

    public function getFilename(Vpc_Interface $page)
    {
        return isset($this->_pageFilenames[$page->getPageId()]) ? $this->_pageFilenames[$page->getPageId()] : '';
    }

    abstract public function findPageByPath($path);

    public function findComponentByClass($class, $startPage = null)
    {
        $ids = $this->_dao->getTable('Vps_Dao_Pages')->findPagesByClass($class);
        $id = (int)array_shift($ids);
        if ($id > 0) {
            $page = $this->findPage($id);
            if ($page) {
                $component = $page->findComponentByClass($class);
                if ($component) {
                    return $component;
                }
            }
        }
        return null;
    }

    public function getTitle($page)
    {
        $data = $this->getPageData($page);
        return isset($data['name']) ? $data['name'] : '';
    }
}
