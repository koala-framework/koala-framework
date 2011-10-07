<?php
class Kwf_Model_DbWithConnection_Dirty_Row extends Kwf_Db_Table_Row
{
    public static $saveCount = 0;

    public static function resetMock()
    {
        self::$saveCount = 0;
    }

    public function save()
    {
        self::$saveCount += 1;
    }
}
