<?php
interface Vps_Auto_Data_Interface
{
    public function load($row);
    public function setFieldname($name);
    public function save(Vps_Model_Row_Interface $row, $data);
    public function delete();
}
