<?php
class Kwf_Model_DbWithConnection_InsertWithId_Row extends Kwf_Model_Db_Row
{
    public static $updateCount;
    public static $saveCount;
    public static $insertCount;

    protected function _afterUpdate()
    {
        parent::_afterUpdate();
        self::$updateCount += 1;
    }

    protected function _afterSave()
    {
        parent::_afterSave();
        self::$saveCount += 1;
    }

    protected function _afterInsert()
    {
        parent::_afterInsert();
        self::$insertCount += 1;
    }
}
