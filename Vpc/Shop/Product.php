<?php
class Vpc_Shop_Product extends Vps_Db_Table_Row
{
    public function __toString()
    {
        return $this->title;
    }
}
