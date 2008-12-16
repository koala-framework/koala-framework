<?php
class Vps_Test_Request_Simple extends Zend_Controller_Request_Simple
{
    public function getResourceName()
    {
        $ret = 'vps_test';
        return $ret;
    }
}