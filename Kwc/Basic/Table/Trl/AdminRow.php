<?php
class Kwc_Basic_Table_Trl_AdminRow extends Kwf_Model_Proxy_Row
{
    public function getReplacedContent($field)
    {
        return Kwc_Basic_Table_RowData::replaceContent($this->$field);
    }
}
