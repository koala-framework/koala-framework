<?php
class Vpc_Basic_Table_Trl_AdminModel extends Vpc_Directories_Item_Directory_Trl_AdminModel
{
    protected function _getComponentId($select)
    {
        foreach ($select->getPart(Vps_Model_Select::WHERE_EQUALS) as $k=>$i)
            if ($k == 'id') return null;
        return parent::_getComponentId($select);
    }

    protected function _getTrlRow($proxiedRow, $componentId)
    {
        $proxyId = $proxiedRow->id;
        $trlRow = $this->_trlModel->getRow($proxyId);
        if (!$trlRow) {
            $trlRow = $this->_trlModel->createRow();
            $trlRow->id = $proxyId;
        }
        return $trlRow;
    }
}
