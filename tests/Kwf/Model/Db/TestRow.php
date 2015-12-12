<?php
class Kwf_Model_Db_TestRow extends Kwf_Db_Table_Row
{
    public function __get($columnName)
    {
        if ($columnName == 'foobar') {
            return 'foobar';
        } else {
            return parent::__get($columnName);
        }
    }

    public function __isset($columnName)
    {
        if ($columnName == 'foobar') {
            return true;
        } else {
            return parent::__isset($columnName);
        }
    }
}
