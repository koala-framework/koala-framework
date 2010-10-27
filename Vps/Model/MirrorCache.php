<?php
class Vps_Model_MirrorCache extends Vps_Model_Proxy
{
    protected $_rowClass = 'Vps_Model_MirrorCache_Row';

    protected $_sourceModel;
    protected $_syncTimeField;

    const SYNC_AFTER_DELAY = false;
    const SYNC_ONCE = true;
    const SYNC_ALWAYS = 2;

    /**
     * Max sync delay in seconds. Default is to 5 minutes
     */
    protected $_maxSyncDelay = 300;

    private $_synchronizeDone = false;

    public function __construct(array $config = array())
    {
        if (isset($config['sourceModel'])) $this->_sourceModel = $config['sourceModel'];
        if (isset($config['syncTimeField'])) $this->_syncTimeField = $config['syncTimeField'];
        if (isset($config['maxSyncDelay'])) $this->_maxSyncDelay = $config['maxSyncDelay'];
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
        $this->synchronize();
        $ret = parent::countRows($where);
        return $ret;
    }

    public function getIds($where=null, $order=null, $limit=null, $start=null)
    {
        $this->synchronize();
        $ret = parent::getIds($where, $order, $limit, $start);
        return $ret;
    }

    public function getRows($where = array(), $order=null, $limit=null, $start=null)
    {
        $this->synchronize();
        $ret = parent::getRows($where, $order, $limit, $start);
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

    private function _getSynchronizeSelect($overrideMaxSyncDelay)
    {
        if ($this->_synchronizeDone) {
            if ($overrideMaxSyncDelay !== self::SYNC_ALWAYS) {
                return false;
            }
        }
        if (!$this->_syncTimeField) {
            throw new Vps_Exception("syncTimeField must be set when using MirrorCache");
        }

        if ($this->_getMaxSyncDelay()) {
            $cache = $this->_getSyncDelayCache();
            if ($overrideMaxSyncDelay === self::SYNC_AFTER_DELAY) {
                $lastSync = $cache->load($this->_getSyncDelayCacheId());
                if ($lastSync && $lastSync + $this->_getMaxSyncDelay() > time()) {
                    return false;
                }
            }
            $cache->save(time(), $this->_getSyncDelayCacheId());
        }

        $this->_synchronizeDone = true; //wegen endlosschleife ganz oben

        Vps_Benchmark::count('mirror sync');

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
        return $select;
    }

    public function synchronize($overrideMaxSyncDelay = self::SYNC_AFTER_DELAY)
    {
        $select = $this->_getSynchronizeSelect($overrideMaxSyncDelay);
        if ($select !== false) {
            $this->getProxyModel()->copyDataFromModel($this->getSourceModel(), $select, array('replace'=>true));
        }
    }

    private function _getMaxSyncDelay()
    {
        if (!is_int($this->_maxSyncDelay) || $this->_maxSyncDelay < 0) {
            throw new Vps_Exception("Variable _maxSyncDelay must be of type integer and bigger or equal to 0");
        }
        return $this->_maxSyncDelay;
    }

    private function _getSyncDelayCacheId()
    {
        return 'mirrorcache_'.md5(
            $this->getSourceModel()->getUniqueIdentifier()
            .$this->getUniqueIdentifier()
            .$this->_syncTimeField
        );
    }

    private function _getSyncDelayCache()
    {
        return Vps_Cache::factory('Core', 'File',
            array(
                'lifetime' => $this->_getMaxSyncDelay(),
                'automatic_serialization' => true
            ),
            array(
                'cache_dir' => 'application/cache/model',
                'file_name_prefix' => 'mirrorcache'
            )
        );
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
        return $r['insertRow'];
    }
}
