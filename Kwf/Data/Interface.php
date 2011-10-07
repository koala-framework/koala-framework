<?php
interface Kwf_Data_Interface
{
    public function load($row);
    public function setFieldname($name);
    public function save(Kwf_Model_Row_Interface $row, $data);
    public function delete();
}
