<?php
class Vps_Srpc_TestClasses_Handler
{
    public $testVar = 'test';

    public function getRow($id)
    {
        if ($id == 3) {
            return array('id' => 3, 'name' => 'Hans');
        }
    }
}