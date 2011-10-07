<?php
class Kwc_News_Directory_Row extends Kwf_Model_Proxy_Row
{
    public function __toString()
    {
        return $this->title;
    }
}
