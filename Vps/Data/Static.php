<?php
class Vps_Data_Static extends Vps_Data_Abstract
{
    private $_content;
    public function __construct($content)
    {
        $this->_content = $content;
    }

    public function load($row)
    {
        return $this->_content;
    }
}
