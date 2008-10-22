<?php
class Vps_Model_ProxyCache extends Vps_Model_Proxy
{
    protected $_cacheSettings;
    protected $_cachedata;
    private $_cache;
    protected $_rowCacheClass = 'Vps_Model_ProxyCache_Row';
    protected $_rowsetCacheClass = 'Vps_Model_ProxyCache_Rowset';

    public function __construct(array $config = array())
    {
        if (isset($config['cacheSettings'])) $this->_cacheSettings = $config['cacheSettings'];

        parent::__construct($config);
    }


    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {

        if (!is_object($where)) {
            if (is_string($where)) $where = array($where);
            $select = $this->select($where, $order, $limit, $start);
        } else {
            $select = $where;
        }
        if ($select->getPart(Vps_Model_Select::WHERE_EQUALS)) {
            $cacheSetting = $this->_getCacheSetting($select);
	        if ($cacheSetting) {
				$cacheId = $this->_getCacheId($cacheSetting);
				if (!isset($this->_cacheData[$cacheId])) {
				    if (!$this->_cacheData[$cacheId] = $this->_getCache()->load($cacheId)) {
						$this->_cacheData[$cacheId] = $this->_getCacheData($cacheSetting);
						$this->_getCache()->save($this->_cacheData[$cacheId], $cacheId);
					}
				}
                $data = array();
				if ($where = $select->getPart(Vps_Model_Select::WHERE_EQUALS)) {
	                $v = implode(array_values($where), '_');
                    $data[] = $this->_cacheData[$cacheId][$v];
	            }
		        return new $this->_rowsetCacheClass(array(
		            'model' => $this,
		            'rowClass' => $this->_rowCacheClass,
		            'cacheData' => $data
		        ));
	        }
        }
        return parent::getRows($where, $order, $limit, $start);
    }

    private function _getCacheSetting ($where)
    {
        $part = $where->getPart(Vps_Model_Select::WHERE_EQUALS);
        $necessary = array_keys($part);
        foreach ($this->_cacheSettings as $cacheSetting) {
            if ($cacheSetting['index'] == $necessary)
                return $cacheSetting;
        }
        return null;
    }

    private function _getCacheId ($cacheSetting)
    {
        $parts = array();
        $parts[] = implode ($cacheSetting['index'], '_');
        $parts[] = implode ($cacheSetting['columns'], '_');
        return implode ($parts, '_');
    }

    private function _getCache() {
        if (!$this->_cache) {
            $frontendOptions = array('lifetime' => 3600, 'automatic_serialization' => true);
            $backendOptions = array('cache_dir' => 'application/cache/model/'); //verzeichnis ändern
            $this->_cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        }

        return $this->_cache;
    }

    private function _getCacheData($cacheSetting)
    {
        $cachedata = array();
        $indexes = $cacheSetting['index'];
        $colindexes = $cacheSetting['columns'];
        $colindexes[] = $this->getPrimaryKey();

        $parts = array();
        $i = 0;
        foreach ($this->getRows() as $row) {
            $searchKey = $this->_getSearchKey($row, $indexes);
            $parts[$searchKey] = array();

            foreach ($colindexes as $key => $colindex) {
                $parts[$searchKey][$colindex] = $row->{$colindex};
            }
            $parts[$searchKey]['internalId'] = $i;
            $i++;
        }
        return $parts;
    }

    private function _getSearchKey($row, $indexes)
    {
        $identifier = array();
        foreach ($indexes as $key => $index) {
            $identifier[] = $row->{$index};
        }
        return implode($identifier, '_');
    }

    public function getRowByCacheData($cacheData)
    {
        $id = $cacheData['internalId'];
        if (!isset($this->_rows[$id])) {
            $this->_rows[$id] = new $this->_rowCacheClass(array(
                'cacheData' => $cacheData,
                'model' => $this
            ));
        }
        return $this->_rows[$id];
    }

    public function clearCache()
    {
        $this->_getCache()->clean(Zend_Cache::CLEANING_MODE_ALL);
        $this->_cacheData = null;
    }

    public function createRow(array $data=array())
    {
        $proxyRow = $this->_proxyModel->createRow($data);
        $ret = new $this->_rowCacheClass(array(
            'row' => $proxyRow,
            'model' => $this
        ));
        $this->_rows[3] = $ret;
        return $ret;
    }

    // nur fürs testen
    public function checkCache ()
    {
        if ($this->_cacheData) return true;
        else return false;
    }

    public function getRowById($select)
    {
        if (!is_object($select)) {
            $select = $this->select($select);
        }
        $select->limit(1);
        return parent::getRows($select)->current();
    }
}
