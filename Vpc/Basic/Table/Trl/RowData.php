<?php
class Vpc_Basic_Table_Trl_RowData extends Vps_Model_Db_Row
{
    public function getReplacedContent($field)
    {
        return Vpc_Basic_Table_RowData::replaceContent($this->$field);
    }
}
