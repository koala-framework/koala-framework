<?php
class Vpc_Advanced_Team_Member_Data_Row extends Vps_Model_Proxy_Row
{
    public function __toString()
    {
        if (!empty($this->firstname) || !empty($this->lastname)) {
            return trim($this->title.' '.trim($this->firstname.' '.$this->lastname));
        }
        return '';
    }
}
