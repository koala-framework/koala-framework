<?php
class E3_Dao_Pages extends Zend_Db_Table
{
    protected $_name = 'pages';

    function fetchChildRows($componentId, $filename='')
    {
        $db = $this->getAdapter();
        $pageId = $db->fetchOne('SELECT id FROM pages WHERE component_id = ?', $componentId);
        if ($pageId) {
            $where = array($db->quoteInto('parent_id = ?', $pageId));
            if ($filename != '') $where[] = $db->quoteInto('filename = ?', $filename);
          return $this->fetchAll($where);
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
    
    function fetchPageById($id)
    {
        $db = $this->getAdapter();
        $rows = $this->fetchAll($db->quoteInto('component_id = ?', $id));
        
        if ($rows->count() != 1) {
            return null;
        }
        
        return $rows->current();
    }
}