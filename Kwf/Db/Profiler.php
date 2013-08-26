<?php
class Kwf_Db_Profiler extends Zend_Db_Profiler
{
    private static $_staticCount = 0;
    private $_count = 0;
    private $_lastQuery;
    private $_longestQuery = null;
    private $_sumTime = 0;
    protected $_logger = null;

    public function __construct($enable)
    {
        parent::__construct($enable);

        $writer = new Zend_Log_Writer_Stream(APP_PATH . '/querylog', 'w');
        $writer->setFormatter(new Zend_Log_Formatter_Simple("%message%\n"));
        $this->_logger = new Zend_Log($writer);

        /*
        foreach (new DirectoryIterator('/tmp') as $item) {
            if (substr($item->getFilename(), 0, 9) == 'querylog.') {
                $time = (int)(substr($item->getFilename(), 9, -2));
                if (time()-$time > 15*60) {
                    @unlink($item->getPathname());
                }
            }
        }
        */
    }

    public function getLogger()
    {
        return $this->_logger;
    }

    public function queryStart($queryText, $queryType = null)
    {
        if (!$this->_enabled) {
            return null;
        }

        Kwf_Benchmark::countLog('dbqueries');

        // make sure we have a query type
        if (null === $queryType) {
            switch (strtolower(substr($queryText, 0, 6))) {
                case 'insert':
                    $queryType = self::INSERT;
                    break;
                case 'update':
                    $queryType = self::UPDATE;
                    break;
                case 'delete':
                    $queryType = self::DELETE;
                    break;
                case 'select':
                    $queryType = self::SELECT;
                    break;
                default:
                    $queryType = self::QUERY;
                    break;
            }
        }

        $this->_lastQuery = new Zend_Db_Profiler_Query($queryText, $queryType);


        $this->_count++;
        self::$_staticCount++;
        if ($this->_logger) {
            $this->_logger->info($this->_count.' ----------------------');
            $this->_logger->debug($queryText);
            //$this->_logger->debug(btString());
        }

        return $this->_count;
    }
    public function getQueryProfile($queryId)
    {
        if ($queryId == $this->_count) {
            return $this->_lastQuery;
        } else {
            return null;
        }
    }
    public function queryEnd($queryId)
    {
        if ($queryId == $this->_count) {

            // Ensure that the query profile has not already ended
            if ($this->_lastQuery->hasEnded()) {
                require_once 'Zend/Db/Profiler/Exception.php';
                throw new Zend_Db_Profiler_Exception("Query with profiler handle '$queryId' has already ended.");
            }

            // End the query profile so that the elapsed time can be calculated.
            $this->_lastQuery->end();

            if (!$this->_longestQuery ||
                $this->_lastQuery->getElapsedSecs()
                    > $this->_longestQuery->getElapsedSecs()) {
                $this->_longestQuery = $this->_lastQuery;
            }

            if ($this->_logger) {
                if ($this->_lastQuery->getQueryParams()) {
                    $this->_logger->debug(print_r($this->_lastQuery->getQueryParams(), true));
                }
                $this->_logger->debug($this->_lastQuery->getElapsedSecs());
                $this->_sumTime += $this->_lastQuery->getElapsedSecs();
                $this->_logger->debug('Sum: '.$this->_sumTime);
            }

        } else {
            throw new Kwf_Exception('Query not found');
        }
    }

    public function logSummary()
    {
        if ($this->_logger) {
            $this->_logger->info('Longest Query:');
            $this->_logger->debug($this->_longestQuery->getQuery());
            $this->_logger->debug($this->_longestQuery->getElapsedSecs().' sec');
        }
    }

    public function getQueryCount()
    {
        return $this->_count;
    }

    public static function getCount()
    {
        return self::$_staticCount;
    }
}
