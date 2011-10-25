<?php
class Kwc_Basic_Table_Trl_RowData extends Kwf_Model_Db_Row
{
    public function getReplacedContent($field)
    {
        return Kwc_Basic_Table_RowData::replaceContent($this->$field);
    }
}
