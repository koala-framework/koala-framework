<?php
class Vps_Auto_Grid_Column_Parent extends Vps_Auto_Grid_Column
{
    public function getMetaData($tableInfo = null)
    {
        $ret = parent::getMetaData($tableInfo);
        if (isset($ret['findParent'])) unset($ret['findParent']);
        return $ret;
    }

    public function getData($row)
    {
        if (!$this->getParentTable()) {
            throw new Vps_Exception("Parent Table not set.");
        }
        $row = $row->findParentRow($this->getParentTable());
        return $row->__toString();
    }
}
