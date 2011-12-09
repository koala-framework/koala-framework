<?php
/**
 * @if deprecated
 * @deprecated
 */
class Kwf_Dao_Row_Pool extends Kwf_Db_Table_Row_Abstract
{
    public function __toString()
    {
        return $this->value;
    }
}
