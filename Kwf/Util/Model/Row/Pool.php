<?php
class Kwf_Util_Model_Row_Pool extends Kwf_Model_Proxy_Row
{
    public function __toString()
    {
        return $this->value;
    }
}
