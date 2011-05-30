<?php
class Vpc_Basic_Table_Row extends Vps_Model_Proxy_Row
{
    public function __get($name)
    {
        if ($name == 'columns') {
            throw new Vps_Exception("Theres no field named 'columns' anymore. Getting the column count is implemented in Vpc_Basic_Table_Component->getColumnCount()");
        } else {
            return parent::__get($name);
        }
    }
}
