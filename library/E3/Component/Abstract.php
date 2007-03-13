<?php
require_once '../application/models/Pages.php';
abstract class E3_Component_Abstract
{
    protected $_pageCollection;
    protected $_pageId;

    function __construct(E3_PageCollection_Abstract $pageCollection, $pageId)
    {
        $this->_pageCollection = $pageCollection;
        $this->_pageId = $pageId;
    }

    public function generateHierachy()
    {
        $pages = new Pages(array('db'=>$this->_pageCollection->getDb()));
        $db = $pages->getAdapter();
        $where = $db->quoteInto('parent_id = ?', $this->_pageId);
        $rows = $pages->fetchAll($where);
        foreach($rows as $pageRow) {
            $this->_pageCollection->createPage($pageRow->id, $pageRow->filename, $pageRow->component, $this);
        }
    }

    function getPageId()
    {
        return $this->_pageId;
    }

    public function getTemplateVars()
    {
        return array();
    }
}
