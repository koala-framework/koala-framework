<?php
class Kwc_Basic_Table_Trl_RowData extends Kwf_Model_Db_Row
{
    public function __get($name)
    {
        if ($name == 'id') {
            $value = parent::__get('master_id');
        } else {
            $value = parent::__get($name);
        }
        return $value;
    }
}
