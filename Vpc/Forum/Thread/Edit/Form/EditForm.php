<?php
class Vpc_Forum_Thread_Edit_Form_EditForm extends Vpc_Posts_Detail_Edit_Form_Form
{
    protected function _getRowByParentRow($parentRow)
    {
        $select = new Vps_Model_Select();
        $select->whereEquals('component_id', $parentRow->cache_child_component_id);
        $select->limit(1);
        return $this->getModel()->fetchAll($select)->current();
    }
}