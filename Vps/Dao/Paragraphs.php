<?php
class Vps_Dao_Paragraphs extends Zend_Db_Table
{
    protected $_name = 'component_paragraphs';
    protected $_primary = array('component_id');
    
    public function fetchParagraphs($componentId, $pageKey, $componentKey)
    {
        $db = $this->getAdapter();
        $where = $db->quoteInto('parent_component_id = ?', $componentId);
        $where .=  $db->quoteInto(' AND parent_page_key = ?', $pageKey);
        $where .=  $db->quoteInto(' AND parent_component_key = ?', $componentKey);
        return $this->fetchAll($where, 'nr');
    }

}