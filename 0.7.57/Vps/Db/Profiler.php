<?php
class Vps_Db_Profiler extends Zend_Db_Profiler
{
    protected $_logger = null;

    public function setLogger($logger)
    {
        $this->_logger = $logger;
    }

    //fkt musste kopiert werden :(
    //damit eigene Vps_Db_Profiler_Query klasse verwendet werden kann
    public function queryStart($queryText, $queryType = null)
    {
        if (!$this->_enabled) {
            return null;
        }

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

        $this->_queryProfiles[] = new Vps_Db_Profiler_Query($queryText, $queryType);

        end($this->_queryProfiles);
        $ret = key($this->_queryProfiles);

        if ($this->_logger) {
            $query = $this->_queryProfiles[$ret];
            $this->_logger->info(count($this->_queryProfiles).' ----------------------');
            $this->_logger->debug($query->getQuery());
            $out = '';
            foreach ($query->getBacktrace() as $bt) {
                if (!isset($bt['file'])) continue;
                if (preg_match('#bootstrap\\.php$#', $bt['file'])) continue;
                if (preg_match('#Db/Profiler\\.php$#', $bt['file'])) continue;
                if (preg_match('#^/www/public/niko/zend103/#', $bt['file'])) continue;
                if (preg_match('#^/www/public/library/#', $bt['file'])) continue;
                $bt['file'] = str_replace('/www/public/niko/vps/', '', $bt['file']);
                $out .= "$bt[file]:$bt[line]\n";
            }
            $this->_logger->debug($out);
        }

        return $ret;
    }
    public function queryEnd($queryId)
    {
        parent::queryEnd($queryId);
        $qp = $this->_queryProfiles[$queryId];
        if ($this->_logger) {
            if ($qp->getElapsedSecs() > 0.1) {
                $this->_logger->info("!!!!!!!!".$qp->getElapsedSecs().'sec');
            } else {
                $this->_logger->info($qp->getElapsedSecs().'sec');
            }
        }
    }
    public function logSummary()
    {
        if (!$this->_logger) return;
        $profiler = $this;
        $totalTime    = $profiler->getTotalElapsedSecs();
        $queryCount   = $profiler->getTotalNumQueries();
        $longestTime  = 0;
        $longestQuery = null;
        foreach ($profiler->getQueryProfiles() as $query) {
            if ($query->getElapsedSecs() > $longestTime) {
                $longestTime  = $query->getElapsedSecs();
                $longestQuery = $query->getQuery();
            }
        }

        $out = "\n\n\n".'Executed ' . $queryCount . ' queries in ' . $totalTime . ' seconds' . "\n";
        $out .= 'Average query length: ' . $totalTime / $queryCount . ' seconds' . "\n";
        $out .= 'Queries per second: ' . $queryCount / $totalTime . "\n";
        $out .= 'Longest query length: ' . $longestTime . "\n";
        $out .= "Longest query: \n" . $longestQuery . "\n";
        $this->_logger->info($out);
    }
}