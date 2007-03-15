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
        return $this->fetchRow($db->quoteInto('parent_id = ?', 0));
    }
}