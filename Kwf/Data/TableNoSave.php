<?php
class Kwf_Data_TableNoSave extends Kwf_Data_Table
{
    public function save(Kwf_Model_Row_Interface $row, $data)
    {
        //do nothing
    }
}
