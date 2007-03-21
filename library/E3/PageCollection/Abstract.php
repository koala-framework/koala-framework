<?php
abstract class E3_PageCollection_Abstract
{
    protected $_pageFilenames = array();
    protected $_pages = array();
    protected $_rootPageId;
    private $_dao;
	
    function __construct(E3_Dao $dao)
    {
    	$this->_dao = $dao;
    }

    public function addPage(E3_Component_Abstract $component, $filename)
    {
        if ($filename == '') {
        	throw new E3_PageCollection_Exception("Pagename must not be empty.");
        }
        $this->_setPage($component, $filename);
    }
    
    private function _setPage(E3_Component_Abstract $component, $filename)
    {
        if ($this->pageExists($component)) {
        	throw new E3_PageCollection_Exception("A page with the same componentId already exists.");
        }
        
        $id = $component->getId();
        $this->_pages[$id] = $component;
        $this->_pageFilenames[$id] = $filename;
    }
    
    public function setRootPage(E3_Component_Abstract $component)
    {
		$this->_setPage($component, '');
        $this->_rootPageId = $component->getId();
    }
    
    public function pageExists($id, $pageTag="", $componentTag="")
    {
    	if ($id instanceof E3_Component_Abstract) {
    		$id = $id->getId();
    		if ($pageTag != "" || $componentTag != "") {
    		    throw new E3_PageCollection_Exception('pageTag and componentTag must be emty when id is a E3_Component_Abstract.');
    		}
    	}
//     	if (!is_int($id)) {
//     		throw new E3_PageCollection_Exception('ID must be an instance of E3_Component_Abstract or an Integer.');
//     	}
    	if ($pageTag != "") $id .= "_".$pageTag;
    	if ($componentTag != "") $id .= "_".$componentTag;
    	return isset($this->_pages[$id]);
    }
    
    public function getRootPage()
    {
    	if (!isset($this->_rootPageId)) {
	    	$pageRow = $this->_dao->getTable('E3_Dao_Pages')->fetchRootPage();

	        $componentClass = $this->_dao->getTable('E3_Dao_Components')
	                            ->getComponentClass($pageRow->component_id);
	        $rootPage = new $componentClass($this->_dao, $pageRow->component_id);
	        $this->setRootPage($rootPage);
    	}
    	return $this->_pages[$this->_rootPageId];
    }

    abstract public function getPageByPath($path);
}
