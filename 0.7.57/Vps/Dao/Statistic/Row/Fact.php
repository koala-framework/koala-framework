<?php
class Vps_Dao_Statistic_Row_Fact extends Vps_Db_Table_Row_Abstract
{
    public function __get($columnName)
    {
        if ($columnName == 'id') {
            $info = $this->getTable()->info();
            $idParts = array();
            foreach ($info['cols'] as $col) {
                if (substr($col, 0, 2) == 'D_') {
                    $idParts[] = $this->$col;
                }
            }
            return implode('', $idParts);
        } else {
            return parent::__get($columnName);
        }
    }

    public function __isset($columnName)
    {
        $allowed = array('id');
        if (in_array($columnName, $allowed)) {
            return true;
        } else {
            return parent::__isset($columnName);
        }
    }
}
