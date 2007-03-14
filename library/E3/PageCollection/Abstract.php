<?php
abstract class E3_PageCollection_Abstract
{
    protected $_pageFilenames = array();
    protected $_pages = array();
    protected $_rootPage;
	
    function __construct(E3_Dao $dao)
    {
        $pageRow = $dao->getTable('E3_Dao_Pages')->fetchRootPage();
        $componentClass = $dao->getTable('E3_Dao_Components')
                            ->getComponentClass($pageRow->componentId);
        $this->_rootPage = new $componentClass($pageRow->componentId, $dao);
    }

    public function addPage(E3_Component_Abstract $component, $filename)
    {
        $id = $component->getComponentId();
        if (isset($this->_pages[$id])) {
        	throw new E3_PageCollection_Exception("A page with the same componentId already exists.");
        }
        $this->_pages[$id] = $component;
        $this->_pageFilenames[$id] = $filename;
    }
    
    public function getRootPage()
    {
    	return $this->_rootPage;
    }

    abstract public function getPageByPath($path);
}
