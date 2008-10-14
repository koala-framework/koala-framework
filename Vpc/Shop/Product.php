<?php
class Vpc_Shop_Product extends Vps_Model_Db_Row
{
    public function __toString()
    {
        return $this->title;
    }
}
