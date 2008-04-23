<?php
class Vps_Db_Profiler_Query extends Zend_Db_Profiler_Query
{
    protected $_backtrace = null;
    public function __construct($query, $queryType)
    {
        parent::__construct($query, $queryType);
        $this->_backtrace = array();
        foreach (debug_backtrace() as $bt) {
            if (!isset($bt['file'])) continue;
            $this->_backtrace[] = "$bt[file]:$bt[line]\n";
        }
    }

    public function getBacktrace()
    {
        return $this->_backtrace;
    }
}
