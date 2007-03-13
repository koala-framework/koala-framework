<?php
class E3_Model_Pages extends Zend_Db_Table
{
    protected $_name = 'pages';

    function fetchChildRowsByComponentId($componentId)
    {
        $db = $this->getAdapter();
        if ($componentId == 0) {
            $pageId = 0;
        } else {
            $pageId = $db->fetchOne('SELECT id FROM pages WHERE component_id = ?', $componentId);
        }
        return $this->fetchAll($db->quoteInto('parent_id = ?', $pageId));
    }
}