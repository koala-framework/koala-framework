<?php
class Vpc_Basic_Table_Trl_AdminModel extends Vpc_Directories_Item_Directory_Trl_AdminModel
{
    protected function _getTrlRow($proxiedRow, $componentId)
    {
        $proxyId = $proxiedRow->id;
        $select = $this->_trlModel->select()
            ->whereEquals('component_id', $componentId)
            ->whereEquals('id', $proxyId);
        $trlRow = $this->_trlModel->getRows($select)->current();
        if (!$trlRow) {
            $trlRow = $this->_trlModel->createRow();
            $trlRow->id = $proxyId;
            $trlRow->component_id = $componentId;
        }
        return $trlRow;
    }
}
