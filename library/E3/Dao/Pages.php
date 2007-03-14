<?php
class E3_Dao_Pages extends Zend_Db_Table
{
    protected $_name = 'pages';

    function fetchChildRowsByComponentId($componentId)
    {
        $db = $this->getAdapter();
/*        
        if ($componentId == 0) {
            $pageId = 0;
        } else {*/
            $pageId = $db->fetchOne('SELECT id FROM pages WHERE component_id = ?', $componentId);
//         }
        return $this->fetchAll($db->quoteInto('parent_id = ?', $pageId));
    }

    function fetchRootPage()
    {
        $db = $this->getAdapter();
        return $this->fetchRow($db->quoteInto('parent_id = ?', 0));
    }
}