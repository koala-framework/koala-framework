<?php
abstract class E3_PageCollection_Abstract
{
    protected $_pageFilenames = array();
    protected $_pages = array();
    protected $_rootPage;

    protected $_db;

    function __construct($db)
    {
        $this->_db = $db;

        $this->_rootPage = new E3_Component_Root(0, $db);
    }

    public function addPage($component, $filename, $componentId)
    {
        $this->_pages[$componentId] = $component;
        $this->_pageFilenames[$componentId] = $filename;
    }
    
    public function getDb()
    {
        return $this->_db;
    }

    abstract public function getParentPage($page);
    abstract public function getChildPages($page);
    abstract public function getPageByPath($path);
}
