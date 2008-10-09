<?php
class Vpc_News_Directory_Row extends Vps_Db_Table_Row_Abstract
{
    public function __toString()
    {
        return $this->title;
    }
}
