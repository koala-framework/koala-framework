<?php
class Vps_Util_Model_Row_Pool extends Vps_Model_Proxy_Row
{
    public function __toString()
    {
        return $this->value;
    }
}
