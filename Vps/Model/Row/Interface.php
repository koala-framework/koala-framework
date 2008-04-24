<?php
interface Vps_Model_Row_Interface
{
    public function __isset($name);
    public function __unset($name);
    public function __get($name);
    public function __set($name, $value);
    public function save();
    public function delete();
    public function toArray();
}
