<?php
abstract class E3_PageCollection_Abstract
{
    protected $_pages = array();
    protected $_pageFilenames = array();
    protected static $_instance = null;
    protected $_db;
    
    function __construct($db)
    {
        $this->createPage(0, '', 'E3_Component_Root', null);
        $this->_db = $db;
    }

    public function createPage($id, $filename, $component, $parentPage)
    {
        $page = new $component($this, $id);
        $this->_pages[$id] = $page;
        $this->_pageFilenames[$id] = $filename;
        return $page;
    }
    
    public function getPageById($id)
    {
        return $this->_pages[$id];
    }
    
    public function getDb()
    {
        return $this->_db;
    }

    abstract public function getParentPage($page);
    abstract public function getChildPages($page);
    abstract public function getPageByPath($path);
}
