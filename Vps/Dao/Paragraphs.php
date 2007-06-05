<?php
class Vps_Dao_Paragraphs extends Vps_Db_Table
{
    protected $_name = 'component_paragraphs';
    protected $_primary = array('component_id');
    
    public function fetchParagraphs($componentId, $pageKey = '', $componentKey = '')
    {
        $db = $this->getAdapter();
        $where = $db->quoteInto('parent_component_id = ?', $componentId);
        $where .=  $db->quoteInto(' AND parent_page_key = ?', $pageKey);
        $where .=  $db->quoteInto(' AND parent_component_key = ?', $componentKey);
        return $this->fetchAll($where, 'nr');
    }
    
    public function fetchParagraphsData($id, $componentId = 0)
    {
        if ($componentId == 0) {
            $where = $this->getAdapter()->quoteInto('p.parent_component_id = ?', $id);
        } else {
            $where = $this->getAdapter()->quoteInto('p.component_id = ?', $componentId);
        }
        $sql = '
            SELECT p.component_id id, p.nr, c.component, c.status
            FROM component_paragraphs p
            LEFT JOIN vps_components c
            ON p.component_id=c.id
            WHERE ' . $where . '
            ORDER BY p.nr
        ';
        $data = $this->getAdapter()->fetchAll($sql);
        if ($componentId > 0 && isset($data[0])) {
            return $data[0];
        } else if ($componentId == 0){
            return $data;
        } else {
            return null;
        }
    }

    public function createParagraph($id, $componentClass, $lastSiblingId = 0, $pageKey = '', $componentKey = '')
    {
        $db = $this->getAdapter();
        $db->beginTransaction();

        // Leere Komponente hinzufügen
        $table = $this->getDao()->getTable('Vps_Dao_Components');
        $componentId = $table->addComponent($componentClass);

        if ($componentId > 0) {

            // Eintrag in Paragraphs-Tabelle
            $insert = array();
            $insert['component_id'] = $componentId;
            $insert['parent_component_id'] = $id;
            $insert['parent_page_key'] = $pageKey;
            $insert['parent_component_key'] = $componentKey;

            if ($this->insert($insert) == $componentId) {
                // Nummerieren
                $row = $this->fetchRow('component_id = ' . $componentId);
                $lastSibling = $this->fetchParagraphsData($id, $lastSiblingId);
                $nr = sizeof($lastSibling) + 1;
                if ($lastSiblingId == 0) {
                    $lastSibling = $this->fetchParagraphsData($id, $lastSiblingId);
                    if (isset($lastSibling['nr'])) {
                        $nr = $lastSibling['nr'] + 1;
                    }
                }
                $row->numberize('nr', $nr, 'parent_component_id = ' . $id);
                
                $db->commit();
                return $componentId;
            }
        }

        $db->rollBack();
        throw new Vps_Dao_Exception('Couldn\'t create Paragraph.');
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
    
    public function moveParagraph($id, $componentId, $direction)
    {
        if ($direction != 'up' && $direction != 'down') {
            throw new Vps_Dao_Exception('Direction must be either "up" or "down".');
        }
        $componentData = $this->fetchParagraphsData($id, $componentId);
        if (!$componentData) {
            throw new Vps_Dao_Exception('Paragraph with id ' . $componentId . ' not found');
        }
        if ($direction == 'up') {
            $nr = $componentData['nr'] - 1;
        } else if ($direction = 'down') {
            $nr = $componentData['nr'] + 1;
        }
        $where = $this->getAdapter()->quoteInto('component_id = ?', $componentId);
        return $this->numberize($where, 'nr', $nr, 'parent_component_id = ' . $id);
    }
}