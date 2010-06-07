<?php
class Vpc_Advanced_Team_Member_Data_Row extends Vps_Model_Proxy_Row
{
    public function __toString()
    {
        if (!empty($this->name)) {
            return $this->name;
        }
        return '';
    }
}
