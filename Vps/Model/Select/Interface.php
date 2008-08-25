<?php
interface Vps_Model_Select_Interface
{
    public function __construct(Vps_Model_Interface $model);
    public function whereEquals($field, $value);
    public function order($field);
    public function limit($start, $count);
}
