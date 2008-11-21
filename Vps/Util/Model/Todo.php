<?php
class Vps_Util_Model_Todo extends Vps_Model_Service
{
    protected function _init()
    {
        $todoUrl = Vps_Registry::get('config')->service->todo->url;
        $this->_client = new Vps_Srpc_Client(array('serverUrl' => $todoUrl));
    }
}
