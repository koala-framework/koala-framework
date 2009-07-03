<?php
class Vpc_Newsletter_Row extends Vps_Model_Db_Row
{
    public function __toString()
    {
        return $this->create_date;
    }
}