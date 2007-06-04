<?php
class Vps_Dao_Paragraphs extends Vps_Db_Table
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
    
    public function createParagraph($parentComponentId, $componentClass, $nr = 0, $pageKey = '', $componentKey = '')
    {
        // Leere Komponente hinzufügen
        $table = $this->getDao()->getTable('Vps_Dao_Components');
        $componentId = $table->addComponent($componentClass);

        // Eintrag in Pages-Tabelle
        $insert = array();
        $insert['component_id'] = $componentId;
        $insert['parent_component_id'] = $parentComponentId;
        $insert['parent_page_key'] = $pageKey;
        $insert['parent_component_key'] = $componentKey;
        $foo = $this->insert($insert);

        // Nummerieren
        $row = $this->fetchRow('component_id = ' . $componentId);
        $row->numberize($nr, 'parent_component_id = ' . $parentComponentId);
        
        return $componentId;
    }
    
    public function deleteParagraph($componentId)
    {
        $db = $this->getAdapter();
        $db->beginTransaction();
        $table = $this->getDao()->getTable('Vps_Dao_Components');
        if ($this->delete($this->getAdapter()->quoteInto('component_id = ?', $componentId)) == 1) {
            if ($table->deleteComponent($componentId) == 1) {
                $db->commit();
                return true;
            }
        }
        $db->rollBack();
        return false;
    }
}