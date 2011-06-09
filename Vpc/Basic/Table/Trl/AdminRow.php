<?php
class Vpc_Basic_Table_Trl_AdminRow extends Vpc_Directories_Item_Directory_Trl_AdminModelRow
{
    public function getReplacedContent($field)
    {
        return Vpc_Basic_Table_RowData::replaceContent($this->$field);
    }
}
