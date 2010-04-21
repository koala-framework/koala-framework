<?php
class Vps_Model_DbWithConnection_Row extends Vps_Db_Table_Row
{
    public static $saveCount = 0;

    public static function resetMock()
    {
        Vps_Model_DbWithConnection_Row::$saveCount = 0;
    }

    public function save()
    {
        Vps_Model_DbWithConnection_Row::$saveCount += 1;
    }
}
