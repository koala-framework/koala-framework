<?php
interface Kwf_Data_Interface
{
    //public function load($row, $info = null); removed from interface to have $info optional
    public function setFieldname($name);
    public function save(Kwf_Model_Row_Interface $row, $data);
    public function delete();
}
