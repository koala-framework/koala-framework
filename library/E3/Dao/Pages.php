<?php
class E3_Dao_Pages extends Zend_Db_Table
{
    protected $_name = 'pages';

    function fetchChildRowsByComponentId($componentId)
    {
        $db = $this->getAdapter();
        $pageId = $db->fetchOne('SELECT id FROM pages WHERE component_id = ?', $componentId);
        if ($pageId) {
        	return $this->fetchAll($db->quoteInto('parent_id = ?', $pageId));
        } else {
        	return array();
        }
    }

    function fetchRootPage()
    {
        $db = $this->getAdapter();
        $rows = $this->fetchAll($db->quoteInto('parent_id = ?', 0));
        
        if ($rows->count() != 1) {
        	throw new E3_Dao_Exception('There must be exactly one row with parent_id 0 in table "pages".');
        }
        
        return $rows->current();
    }
}