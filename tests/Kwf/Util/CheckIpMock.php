<?php

class Kwf_Util_CheckIpMock extends Kwf_Util_Check_Ip
{
    protected function _getAllowedAddresses()
    {
        return array('192.168.0.1', '192.168.0.2', '192.168.*.9');
    }
}
