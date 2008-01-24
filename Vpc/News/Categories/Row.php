<?php
class Vpc_News_Categories_Row extends Vps_Db_Table_Row
{
    public function __toString()
    {
        return $this->category;
    }
}
