<?php
class Vpc_News_CategoryRow extends Vps_Db_Table_Row
{
    public function __toString()
    {
        return $this->category;
    }
}
