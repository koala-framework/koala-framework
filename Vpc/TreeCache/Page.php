<?php
class Vpc_TreeCache_Page extends Vpc_TreeCache_TablePage
{
    protected $_tableName = 'Vps_Dao_Pages';
    protected $_componentClass = 'row';
    protected $_idSeparator = false;
    
    protected function _formatConstraints($parentData, $constraints) {
        $where = parent::_formatConstraints($parentData, $constraints);
        $where['visible = ?'] = Zend_Registry::get('config')->showInvisible;
        if (isset($constraints['id'])) {
            $where['id = ?'] = $constraints['id'];
        } else {
            foreach ($constraints as $key => $val) {
                if ($key == 'filename') {
                    if ($val == '') {
                        $where['is_home = ?'] = 1;
                    } else {
                        $where['filename = ?'] = $val;
                    }
                } else if (is_null($val)) {
                    $where[$key . ' IS NULL'] = '';
                } else {
                    $where[$key . ' = ?'] = $val;
                }
            }
            if ($parentData instanceof Vps_Component_Data_Root) {
                $where['parent_id IS NULL'] = '';
            } else {
                $componentId = $parentData->getComponentId();
                if (!is_numeric($componentId)) { return array(); }
                $where['parent_id = ?'] = $parentData->getComponentId();
            }
        }
        return $where;
    }
}
