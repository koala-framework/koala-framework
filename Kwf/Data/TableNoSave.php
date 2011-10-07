<?php
class Vps_Data_TableNoSave extends Vps_Data_Table
{
    public function save(Vps_Model_Row_Interface $row, $data)
    {
        //do nothing
    }
}
