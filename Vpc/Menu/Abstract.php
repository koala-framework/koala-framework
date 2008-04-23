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
    
    protected function _getMenuData($parentComponentId = null)
    {
        $tc = $this->getTreeCacheRow()->getTable();

        // Hauptmenü
        $select = $tc->select()
            ->where('menu = 1');
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
        $rows = $tc->fetchAll($select);
        $return = array();
        foreach ($rows as $i => $row) {
            $class = '';
            if ($i == 0) $class .= ' first';
            if ($i == sizeof($rows)-1) $class .= ' last';
            $isCurrent = in_array($row->component_id, $this->_getCurrentPageIds());
            if ($isCurrent) $class .= ' current';
            $data = array(
                'componentId'  => $row->component_id,
                'text'         => $row->name,
                'current'      => $isCurrent,
                'class'        => trim($class),
            );
            if (!$this->_showInvisible()) {
                $data['href'] = $row->url;
                $data['rel'] = $row->rel;
            } else {
                $data['href'] = $row->url_preview;
                $data['rel'] = $row->rel_preview;
            }
            $return[] = $data;
        }
        return $return;
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
