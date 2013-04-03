<?php
class Kwc_Basic_Table_Trl_RowData extends Kwf_Model_Db_Row
{
    public function getReplacedContent($field)
    {
        return Kwc_Basic_Table_RowData::replaceContent($this->$field);
    }

    public function __get($name)
    {
        $value = parent::__get($name);
        if (!$value) {
            //FIXME load from master-row
        }
        return $value;
    }
}
