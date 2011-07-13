<?php
class Vps_Model_MirrorCache extends Vps_Model_Proxy
{
    protected $_rowClass = 'Vps_Model_MirrorCache_Row';

    protected $_sourceModel;
    protected $_syncTimeField = null;
    protected $_truncateBeforeFullImport = false;

    const SYNC_AFTER_DELAY = false;
    const SYNC_ONCE = true;
    const SYNC_ALWAYS = 2;

    /**
     * Max sync delay in seconds. Default is to 5 minutes
     */
    protected $_maxSyncDelay = 300;

    private $_synchronizeDone = false;
    private $_lockSync;

    public function __construct(array $config = array())
    {
        if (isset($config['sourceModel'])) $this->_sourceModel = $config['sourceModel'];
        if (isset($config['syncTimeField'])) $this->_syncTimeField = $config['syncTimeField'];
        if (isset($config['maxSyncDelay'])) $this->_maxSyncDelay = $config['maxSyncDelay'];
        if (isset($config['truncateBeforeFullImport'])) $this->_truncateBeforeFullImport = $config['truncateBeforeFullImport'];
        parent::__construct($config);
    }

    public function getSourceModel()
    {
        if (is_string($this->_sourceModel)) {
            $this->_sourceModel = Vps_Model_Abstract::getInstance($this->_sourceModel);
        }
        return $this->_sourceModel;
    }

    public function countRows($where = array())
    {
        $this->_synchronize();
        $ret = parent::countRows($where);
        $this->_unlockSync();
        return $ret;
    }

    public function getIds($where=null, $order=null, $limit=null, $start=null)
    {
        $this->_synchronize();
        $ret = parent::getIds($where, $order, $limit, $start);
        $this->_unlockSync();
        return $ret;
    }

    public function getRows($where = array(), $order=null, $limit=null, $start=null)
    {
        $this->_synchronize();
        $ret = parent::getRows($where, $order, $limit, $start);
        $this->_unlockSync();
        return $ret;
    }

    /**
     * @deprecated
     * Use synchronize instead
     */
    public function checkCache()
    {
        $this->synchronize();
    }

    private function _getLastSyncFile()
    {
        return 'application/cache/model/mirrorcache_'.md5(
                        $this->getSourceModel()->getUniqueIdentifier()
                        .$this->getUniqueIdentifier()
                        .$this->_syncTimeField
                    );
    }

    private function _unlockSync()
    {
        fclose($this->_lockSync);
        $this->_lockSync = null;
    }

    private function _lockSync($write = false)
    {
        if ($this->_lockSync) {
            throw new Vps_Exception('Already locked');
        }
        $this->_lockSync = fopen($this->_getLastSyncFile().'.lock', "w");

        $startTime = microtime(true);
        while(true) {
            if (flock($this->_lockSync, ($write ? LOCK_EX : LOCK_SH) | LOCK_NB)) {
                break;
            }
            if (microtime(true)-$startTime > 120) {
                throw new Vps_Exception("Lock Failed, locked by");
            }
            usleep(rand(0, 100)*100);
        }
        fwrite($this->_lockSync, getmypid());
    }

    /**
     * Rückgabewert false: kein sync notwendig
     *              null: alles syncen
     *              Vps_Model_Select: das was im select steht syncen
     */
    protected function _getSynchronizeSelect($overrideMaxSyncDelay)
    {
        if ($this->_synchronizeDone) {
            if ($overrideMaxSyncDelay !== self::SYNC_ALWAYS) {
                $this->_lockSync();
                return false; //es wurde bereits synchronisiert
            }
        }

        if ($this->_getMaxSyncDelay()) {
            if ($overrideMaxSyncDelay === self::SYNC_AFTER_DELAY) {
                $lastSyncFile = $this->_getLastSyncFile();
                $lastSync = false;
                if (file_exists($lastSyncFile)) {
                    $lastSync = file_get_contents($lastSyncFile);
                }
                if ($lastSync && $lastSync + $this->_getMaxSyncDelay() > time()) {
                    $this->_lockSync();
                    return false; //maxSyncDelay wurde noch nicht erreicht
                }
            }
        }

        $this->_synchronizeDone = true; //wegen endlosschleife ganz oben

        Vps_Benchmark::count('mirror sync');

        if (!$this->_syncTimeField) {
            // kein modified feld vorhanden, alle kopieren
            $select = null;
        } else {
            $syncField = $this->_syncTimeField;
            $proxyModel = $this->getProxyModel();
            $pr = $proxyModel->getRow($proxyModel->select()->order($syncField, 'DESC'));
            $cacheTimestamp = $pr ? $pr->$syncField : null;

            if ($cacheTimestamp && !preg_match('/^[0-9]{4,4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/', $cacheTimestamp)) {
                throw new Vps_Exception("syncTimeField must be of type datetime (yyyy-mm-dd hh:mm:ss) when using mirror cache");
            }

            $sourceModel = $this->getSourceModel();
            if (!$cacheTimestamp) {
                // kein cache vorhanden, alle kopieren
                $select = null;
            } else {
                $select = $sourceModel->select()->where(
                    new Vps_Model_Select_Expr_HigherDate($this->_syncTimeField, $cacheTimestamp)
                );
            }
        }

        if (is_null($select)) {
            //wenn alles importiert wird write lock befor maxSyncFile geschrieben wird
            $this->_lockSync(true);
        } else {
            $this->_lockSync();
        }
        if ($this->_getMaxSyncDelay()) {
            //letzten sync zeitpunkt schreiben
            file_put_contents($this->_getLastSyncFile(), time());
        }
        return $select;
    }

    public final function synchronize($overrideMaxSyncDelay = self::SYNC_AFTER_DELAY)
    {
        $this->_synchronize($overrideMaxSyncDelay);
        $this->_unlockSync();
    }

    /**
     * Wird aufgerufen bevor ein sync stattfindet, nicht wenn kein sync notwendig ist
     */
    protected function _beforeSynchronize()
    {
    }

    private function _synchronize($overrideMaxSyncDelay = self::SYNC_AFTER_DELAY)
    {
        $select = $this->_getSynchronizeSelect($overrideMaxSyncDelay);
        if ($select !== false) {

            $start = microtime(true);

            $this->_beforeSynchronize();
            // it's possible to use $this->getProxyModel()->copyDataFromModel()
            // but if < 20 rows are copied, array is faster than sql or csv
            $format = null;
            if (!is_null($select)) {
                if (in_array(self::FORMAT_ARRAY, $this->getProxyModel()->getSupportedImportExportFormats())
                    && in_array(self::FORMAT_ARRAY, $this->getSourceModel()->getSupportedImportExportFormats())
                ) {
                    $format = self::FORMAT_ARRAY;
                }
            }
            if (!$format) {
                $format = self::_optimalImportExportFormat($this->getProxyModel(), $this->getSourceModel());
            }

            $options = array();
            $data = $this->getSourceModel()->export($format, $select);
            $exportTime = microtime(true)-$start;

            $start = microtime(true);
            if (!$select && $this->_truncateBeforeFullImport) {
                $this->getProxyModel()->deleteRows($this->getProxyModel()->select());
            } else {
                $options['replace'] = true;
            }
            $this->getProxyModel()->import($format, $data, $options);
            $importTime = microtime(true)-$start;

            $tableName = '';
            if ($this->getProxyModel() instanceof Vps_Model_Db) $tableName = $this->getProxyModel()->getTableName();
            $msg = date('Y-m-d H:i:s').' '.str_replace('cache_', '', $tableName).' '.$format;
            if (is_array($data)) {
                $msg .= " ".count($data)." entries";
            } else if (is_string($data)) {
                $msg .= " ".strlen($data)." bytes";
            }
            $msg .= ' export: '.round($exportTime, 2).'s';
            $msg .= ' import: '.round($importTime, 2).'s';
            //$msg .= ' SELECT: '.str_replace("\n", " ", print_r($select, true));
            file_put_contents('application/log/mirrorcache', $msg."\n", FILE_APPEND);
        }
    }

    private function _getMaxSyncDelay()
    {
        if (!is_int($this->_maxSyncDelay) || $this->_maxSyncDelay < 0) {
            throw new Vps_Exception("Variable _maxSyncDelay must be of type integer and bigger or equal to 0");
        }
        return $this->_maxSyncDelay;
    }

    public function synchronizeAndUpdateRow($data)
    {
        $select = $this->_getSynchronizeSelect(self::SYNC_ONCE);

        $call = array();
        if ($select !== false) {
            $format = self::_optimalImportExportFormat($this->getSourceModel(), $this->getProxyModel());
            $call['export'] = array($format, $select);
        }
        $call['updateRow'] = array($data);
        $r = $this->getSourceModel()->callMultiple($call);
        if ($select !== false) {
            $this->getProxyModel()->import($format, $r['export'], array('replace' => true));
        }
        $this->getProxyModel()->import(self::FORMAT_ARRAY,
            array($r['updateRow']),
            array('replace' => true));

        $this->_unlockSync();
        return $r['updateRow'];
    }

    public function synchronizeAndInsertRow($data)
    {
        $select = $this->_getSynchronizeSelect(self::SYNC_ONCE);

        $call = array();
        if ($select !== false) {
            $format = self::_optimalImportExportFormat($this->getSourceModel(), $this->getProxyModel());
            $call['export'] = array($format, $select);
        }
        $call['insertRow'] = array($data);
        $r = $this->getSourceModel()->callMultiple($call);
        if ($select !== false) {
            $this->getProxyModel()->import($format, $r['export'], array('replace' => true));
        }
        $this->getProxyModel()->import(self::FORMAT_ARRAY,
            array($r['insertRow']),
            array('replace' => true));

        $this->_unlockSync();
        return $r['insertRow'];
    }
}
