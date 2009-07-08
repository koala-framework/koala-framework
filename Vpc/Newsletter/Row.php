<?php
class Vpc_Newsletter_Row extends Vps_Model_Proxy_Row
{
    public function __toString()
    {
        return $this->create_date;
    }
}