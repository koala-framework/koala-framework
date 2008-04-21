<?php
class Vps_Dao_Statistic_Row_Dimension extends Vps_Db_Table_Row_Abstract
{
    public function __toString()
    {
        $info = $this->getTable()->info();
        $field = $info['cols'][1];
        return $this->$field;
    }
}
