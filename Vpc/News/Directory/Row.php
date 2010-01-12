<?php
class Vpc_News_Directory_Row extends Vps_Model_Proxy_Row
{
    public function __toString()
    {
        return $this->title;
    }
}
