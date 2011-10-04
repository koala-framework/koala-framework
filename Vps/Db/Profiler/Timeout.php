<?php
class Vps_Db_Profiler_Timeout extends Vps_Db_Profiler_Count
{
    private $_timeout;
    private $_lastQueryStart;
    private $_lastQueryText;

    public function __construct($timeout, $enabled = false)
    {
        parent::__construct($enabled);
        $this->_timeout = $timeout;
    }

    public function queryStart($queryText, $queryType = null)
    {
        if (!$this->_enabled) {
            return null;
        }
        $this->_lastQuery = new Zend_Db_Profiler_Query($queryText, $queryType);
        parent::queryStart($queryText, $queryType);
        return $this->_count;
    }

    public function queryEnd($queryId)
    {
        if ($queryId == $this->_count) {
            if ($this->_lastQuery->hasEnded()) {
                throw new Zend_Db_Profiler_Exception("Query with profiler handle '$queryId' has already ended.");
            }
            $this->_lastQuery->end();
            $time = $this->_lastQuery->getElapsedSecs();
            if ($time > $this->_timeout) {
                $e = new Vps_Exception('Query timed out with '.$time.'s. \''.$this->_lastQuery->getQuery().'\'');
                $e->logOrThrow();
            }
        } else {
            throw new Vps_Exception('Query not found');
        }
    }

    public function getQueryProfile($queryId)
    {
        if ($queryId == $this->_count) {
            return $this->_lastQuery;
        } else {
            throw new Vps_Exception('Query not found');
        }
    }

}
