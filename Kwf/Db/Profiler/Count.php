<?php
class Vps_Db_Profiler_Count extends Zend_Db_Profiler
{
    protected $_count = 0;
    public function queryStart($queryText, $queryType = null)
    {
        if (!$this->_enabled) {
            return null;
        }
        $this->_count++;
        return null;
    }

    public function queryEnd($queryId)
    {
    }

    public function getQueryCount()
    {
        return $this->_count;
    }
}
