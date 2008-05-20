<?php
class Vpc_Menu_Abstract extends Vpc_Abstract
{
    private $_currentPageIds;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'level' => 'main' // (string)pagetype oder (int)ebene
        ));
    }

    protected function _select()
    {
        return $this->getTreeCacheRow()->getTable()->select();
    }

    protected function _getMenuData($parentComponentId = null)
    {
        $select = $this->_select();
        // Hauptmenü
        if ($parentComponentId) {
            $select->where('parent_component_id = ?', $parentComponentId);
        } else {
            $level = $this->_getSetting('level');
            if (is_string($level)) {
                $select->from('vps_tree_cache');
                $select->from('vps_pages', array());
                $select->where('vps_pages.id = vps_tree_cache.component_id');
                $select->where('vps_pages.type = ?', $level);
                $select->where('ISNULL(parent_component_id)');
            } else {
                $currentPageIds = array_reverse($this->_getCurrentPageIds());
                if (isset($currentPageIds[$level - 1])) {
                    $select->where('parent_component_id = ?', $currentPageIds[$level - 1]);
                } else {
                    $select->where('1 = 2');
                }
            }
        }
        if (!$this->_showInvisible()) {
            $select->where('vps_tree_cache.visible = ?', 1);
        }
        $select->order('pos');

        return $this->getTreeCacheRow()->getTable()
                    ->fetchAll($select)->toMenuData($this->_getCurrentPageIds());
    }
    
    // Array mit IDs von aktueller Seiten und Parent Pages
    protected function _getCurrentPageIds()
    {
        if (!isset($this->_currentPageIds)) {
            $this->_currentPageIds = array();
            $p = $this->getTreeCacheRow();
            do {
                if ($p->url) {
                    $this->_currentPageIds[] = $p->component_id;
                }
            } while ($p = $p->findParentPage());
        }
        return $this->_currentPageIds;
    }
}
