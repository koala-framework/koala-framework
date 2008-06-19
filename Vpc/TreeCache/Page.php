<?php
class Vpc_TreeCache_Page extends Vpc_TreeCache_TablePage
{
    protected $_tableName = 'Vps_Dao_Pages';
    protected $_componentClass = 'row';
    protected $_idSeparator = false;
    
    protected function _formatConstraints($parentData, $constraints) {
        $select = parent::_formatConstraints($parentData, $constraints);
        if (!$select) return null;
        if (!Zend_Registry::get('config')->showInvisible) {
            $select->where('visible = ?', 1);
        }
        if (!isset($constraints['id'])) {
            if (isset($constraints['filename'])) {
                if ($constraints['filename'] == '') {
                    $select->where('is_home = ?', 1);
                } else {
                    $select->where('filename = ?', $constraints['filename']);
                }
            }
            if ($parentData instanceof Vps_Component_Data_Root) {
                $select->where('parent_id IS NULL');
            } else {
                $componentId = $parentData->componentId;
                if (!is_numeric($componentId)) { return array(); }
                $select->where('parent_id = ?', $parentData->componentId);
            }
            if (isset($constraints['type'])) {
                $select->where('type = ?', $constraints['type']);
            }
        }
        return $select;
    }
}
