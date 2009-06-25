<?php
class Vpc_News_Directory_Row extends Vps_Model_Db_Row
{
    public function __toString()
    {
        return $this->title;
    }
}
