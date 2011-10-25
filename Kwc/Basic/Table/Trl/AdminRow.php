<?php
class Kwc_Basic_Table_Trl_AdminRow extends Kwc_Directories_Item_Directory_Trl_AdminModelRow
{
    public function getReplacedContent($field)
    {
        return Kwc_Basic_Table_RowData::replaceContent($this->$field);
    }
}
