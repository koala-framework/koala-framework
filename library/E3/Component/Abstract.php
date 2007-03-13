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
        $db = $this->_pageCollection->getDb();
        $sql = $db->quoteInto('SELECT * FROM pages WHERE parent_id = ?', $this->_pageId);
        $result = $db->query($sql);
        $rows = $result->fetchAll();
        foreach($rows as $pageRow) {
            $this->_pageCollection->createPage($pageRow['id'], $pageRow['filename'], $pageRow['component'], $this);
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
