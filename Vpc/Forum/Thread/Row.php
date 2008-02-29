<?php
class Vpc_Forum_Thread_Row extends Vps_Db_Table_Row_Abstract
{
    public function __toString()
    {
        return $this->subject;
    }
}