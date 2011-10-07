<?php
class Vps_Test_ResultLogger extends PHPUnit_TextUI_ResultPrinter
{
    protected $_log = '';
    public function __construct($verbose = FALSE)
    {
        parent::__construct();
    }

    protected function writeProgress($progress)
    {
        //empty
    }

    public function write($buffer)
    {
        $this->_log .= $buffer;
    }

    public function getContent()
    {
        return $this->_log;
    }
}
