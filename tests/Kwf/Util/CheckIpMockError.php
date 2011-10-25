<?php

class Kwf_Util_CheckIpMockError extends Kwf_Util_Check_Ip
{
    // müsste eigentlich ein array zurückgeben
    protected function _getAllowedAddresses()
    {
        return '192.168.0.1';
    }
}
