<?php
abstract class E3_PageCollection_Abstract
{
    protected $_pageFilenames = array();
    protected $_pages = array();
    protected $_rootPageId;
    protected $_addDecorator = false;
    private $_dao;
    protected static $_instance = null;
    

    function __construct(E3_Dao $dao)
    {
      $this->_dao = $dao;
    }
    
    public static function getInstance()
    {
        if (null === self::$_instance) {
            $dao = Zend_Registry::get('dao');
            
            $pageCollectionConfig = new Zend_Config_Ini('../application/config.ini', 'pagecollection');
            $pageCollection = new $pageCollectionConfig->pagecollection->type($dao);
            $pageCollection->setAddDecorator($pageCollectionConfig->pagecollection->addDecorator);
            
            self::$_instance = $pageCollection;
        }

        return self::$_instance;
    }

    public function addPage(E3_Component_Interface $component, $filename)
    {
        if ($filename == '') {
          throw new E3_PageCollection_Exception("Pagename must not be empty.");
        }
        $decoratedComponent = $this->addDecoratorsToComponent($component);
        $this->_setPage($decoratedComponent, $filename);
    }

    public function setAddDecorator($decorator)
    {
        //todo: raise exception if no string, or class does'nt exist, or class doesn't inherit E3_Component_Decorator_Abstract
        $this->_addDecorator = $decorator;
    }

    protected function addDecoratorsToComponent(E3_Component_Abstract $component)
    {
        if ($this->_addDecorator) {
            return new $this->_addDecorator($this->_dao, $component);
        } else {
            return $component;
        }
    }

    private function _setPage(E3_Component_Interface $component, $filename)
    {
        if ($this->pageExists($component)) {
          throw new E3_PageCollection_Exception("A page with the same componentId already exists.");
        }

        $id = $component->getId();
        $this->_pages[$id] = $component;
        $this->_pageFilenames[$id] = $filename;
    }

    public function setRootPage(E3_Component_Interface $component)
    {
    $this->_setPage($this->addDecoratorsToComponent($component), '');
        $this->_rootPageId = $component->getId();
    }

    public function pageExists($id, $pageTag="", $componentTag="")
    {
      if ($id instanceof E3_Component_Interface) {
        $id = $id->getId();
        if ($pageTag != "" || $componentTag != "") {
            throw new E3_PageCollection_Exception('pageTag and componentTag must be emty when id is a E3_Component_Interface.');
        }
      }
//     	if (!is_int($id)) {
//     		throw new E3_PageCollection_Exception('ID must be an instance of E3_Component_Interface or an Integer.');
//     	}
      if ($pageTag != "") $id .= "_".$pageTag;
      if ($componentTag != "") $id .= "|".$componentTag;
      return isset($this->_pages[$id]);
    }

    public function getRootPage()
    {
      if (!isset($this->_rootPageId)) {
        $pageRow = $this->_dao->getTable('E3_Dao_Pages')->fetchRootPage();
            $className = $this->_dao->getTable('E3_Dao_Components')->getComponentClass($pageRow->component_id);
          $rootPage = new $className($this->_dao, $pageRow->component_id);
          $this->setRootPage($rootPage);
      }
      return $this->_pages[$this->_rootPageId];
    }
    
    abstract public function getPageByPath($path);
}
