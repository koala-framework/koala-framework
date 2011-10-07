<?php
/**
 * @deprecated
 */
class Vps_Dao_Row_Pool extends Vps_Db_Table_Row_Abstract
{
    public function __toString()
    {
        return $this->value;
    }
}
