<?php
class Vps_Db_Profiler_Query extends Zend_Db_Profiler_Query
{
    protected $_backtrace = null;
    public function __construct($query, $queryType)
    {
        parent::__construct($query, $queryType);
        $this->_backtrace = debug_backtrace();
    }
    public function getBacktrace()
    {
        return $this->_backtrace;
    }
}
