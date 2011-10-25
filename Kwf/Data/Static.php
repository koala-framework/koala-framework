<?php
class Kwf_Data_Static extends Kwf_Data_Abstract
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
