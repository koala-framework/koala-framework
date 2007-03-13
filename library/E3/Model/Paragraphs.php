<?php
class E3_Model_Paragraphs extends Zend_Db_Table
{
    protected $_name = 'component_paragraphs';
    protected $_primary = 'parent_component_id';

    public function fetchParagraphsByParentComponentId($componentId)
    {
        $where = $this->_db->quoteInto('parent_component_id = ?', $componentId);
        return $this->fetchAll($where);
    }
}