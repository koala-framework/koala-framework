<?php
class Kwf_Test_Request_Simple extends Zend_Controller_Request_Simple
{
    public function getResourceName()
    {
        $ret = 'kwf_test';
        return $ret;
    }
}