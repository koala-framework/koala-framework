<?php
class Vps_Auto_Grid_Column_Parent extends Vps_Auto_Grid_Column
{
    public function getMetaData($tableInfo = null)
    {
        $ret = parent::getMetaData($tableInfo);
        if (isset($ret['parentTable'])) unset($ret['parentTable']);
        if (isset($ret['parentField'])) unset($ret['parentField']);
        return $ret;
    }

    public function getData(Zend_Db_Table_Row_Abstract $row, $role)
    {
        if (!$this->getParentTable()) {
            throw new Vps_Exception("Parent Table not set.");
        }
        $row = $row->findParentRow($this->getParentTable());
        if ($this->getParentField()) {
            $f = $this->getParentField();
            return $row->$f;
        } else {
            return $row->__toString();
        }
    }
}
