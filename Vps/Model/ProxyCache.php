<?php
class Vps_Model_ProxyCache extends Vps_Model_Proxy
{
    protected $_cacheSettings;
    protected $_cacheData = array();
    private $_cache;
    protected $_rowClass = 'Vps_Model_ProxyCache_Row';
    protected $_rowsetClass = 'Vps_Model_ProxyCache_Rowset';

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
        if ($select->getPart(Vps_Model_Select::WHERE_EQUALS) || $select->getPart(Vps_Model_Select::WHERE_NULL)) {
            $cacheSetting = $this->_getCacheSetting($select);
            if ($cacheSetting) {


                $cacheId = $this->_getCacheId($cacheSetting);

                if (!isset($this->_cacheData[$cacheId])) {
                    if (!$this->_cacheData[$cacheId] = $this->_getCache()->load($cacheId)) {
                        $this->_cacheData[$cacheId] = $this->_getCacheData($cacheSetting);
                        $this->_getCache()->save($this->_cacheData[$cacheId], $cacheId);
                    }
                }
                $whereEquals = $select->getPart(Vps_Model_Select::WHERE_EQUALS);
                $whereNull = $select->getPart(Vps_Model_Select::WHERE_NULL);
                $values = array();
                foreach ($cacheSetting['index'] as $value) {
                    if ($whereEquals) {
                        if (isset($whereEquals[$value])) $values[] = $this->_escapeSearchKeyElement($whereEquals[$value]);
                    }
                    if ($whereNull) {
                        foreach (array_values($whereNull) as $whereKey => $whereValue) {
                            if ($whereValue == $value)
                                $values[] = $this->_escapeSearchKeyElement(null);
                        }
                    }
                }
                $valuesbefore = $values;
                $v = implode($values, '_');

                if (isset($this->_cacheData[$cacheId][$v]))
                    $data = $this->_cacheData[$cacheId][$v];
                else {
                    $data = array();
                }



                return new $this->_rowsetClass(array(
                    'model' => $this,
                    'rowClass' => $this->_rowClass,
                    'cacheData' => $data
                ));
            }
        }
        return parent::getRows($where, $order, $limit, $start);
    }

    private function _getCacheSetting ($where)
    {
        $necessary = array();
        $whereEquals = $where->getPart(Vps_Model_Select::WHERE_EQUALS);
        $whereNull = $where->getPart(Vps_Model_Select::WHERE_NULL);

        foreach ($this->_cacheSettings as $cacheSetting) {
            $check = true;
            foreach ($cacheSetting['index'] as $value) {
                $cacheSettingCheck = false;
                if ($whereEquals) {
                    if (isset($whereEquals[$value])) {
                        $values[] = $this->_escapeSearchKeyElement($whereEquals[$value]);
                        $cacheSettingCheck = true;
                    }
                }
                if ($whereNull) {
                    foreach (array_values($whereNull) as $whereKey => $whereValue) {
                        if ($whereValue == $value) {
                            $values[] = $this->_escapeSearchKeyElement(null);
                                $cacheSettingCheck = true;
                        }
                    }
                }
                if ($cacheSettingCheck == false) {
                    $check = false;
                }
            }
            if ($check) {
                return $cacheSetting;
            }
        }
        return null;
    }

    private function _getCacheId ($cacheSetting)
    {
        $parts = array();
        $parts[] = $this->getUniqueIdentifier();
        $parts[] = implode ($cacheSetting['index'], '_');
        $parts[] = implode ($cacheSetting['columns'], '_');
        return implode ($parts, '_');
    }

    private function _getCache()
    {
        if (!$this->_cache) {
            $frontendOptions = array(
                'lifetime' => 3600, //TODO: warum lifetime??
                'automatic_serialization' => true
            );
            $backendOptions = array(
                'cache_dir' => 'application/cache/model',
                'file_name_prefix' => 'proxycache'
            );
            $this->_cache = Vps_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        }

        return $this->_cache;
    }

    private function _getCacheData($cacheSetting)
    {

        $cachedata = array();
        $indexes = $cacheSetting['index'];
        $colindexes = $cacheSetting['columns'];
        $colindexes[] = $this->getPrimaryKey();

        $ret = array();
        $i = 0;

        foreach ($this->getRows() as $row) {
            $searchKey = $this->_getSearchKey($row, $indexes);
            $parts = array();
            foreach ($colindexes as $key => $colindex) {
                if (is_object($row->{$colindex})) {
                    throw new Vps_Exception(get_class($row->{$colindex})." is a object");
                }
                $parts['data'][$colindex] = $row->{$colindex};
            }
            if (!$this->getPrimaryKey()) {
                $parts['id'] = $i;
                $i++;
            } else {
                $parts['id'] = $row->{$this->getPrimaryKey()};
            }
            $ret[$searchKey][$parts['id']] = $parts;
        }
        return $ret;
    }

    private function _getSearchKey($row, $indexes)
    {
        $identifier = array();
        foreach ($indexes as $key => $index) {
           $identifier[] = $this->_escapeSearchKeyElement($row->{$index});
        }
        $ret = implode(($identifier), '_');
        return $ret;
    }

    private function _escapeSearchKeyElement($element)
    {
        if ($element === null){
            return '_';
        }
        $element = str_replace("\\", "\\\\", $element);
        $element = str_replace("_", "\_", $element);
        return $element;
    }
    public function getRowByCacheData($id, $cacheData)
    {
        if (!isset($this->_rows[$id])) {
            $this->_rows[$id] = new $this->_rowClass(array(
                'cacheData' => $cacheData,
                'id' => $id,
                'model' => $this
            ));
        }
        return $this->_rows[$id];
    }

    public function getRowByProxiedRow($proxiedRow)
    {
        $id = $proxiedRow->{$this->getPrimaryKey()};
        if (!isset($this->_rows[$id])) {
            $this->_rows[$id] = new $this->_rowClass(array(
                'row' => $proxiedRow,
                'model' => $this
            ));
        }
        return $this->_rows[$id];
    }

    public function getRowById($select)
    {
        if (!is_object($select)) {
            $select = $this->select($select);
        }
        $select->limit(1);
        return $this->getProxyModel()->getRow($select);
    }

    public function clearCache()
    {
        $this->_getCache()->clean(Zend_Cache::CLEANING_MODE_ALL);
        $this->_cacheData = array();
    }

    public function clearCacheStore()
    {
        $this->_getCache()->clean(Zend_Cache::CLEANING_MODE_ALL);
    }

    public function createRow(array $data=array())
    {
        $proxyRow = $this->getProxyModel()->createRow($data);
        $ret = new $this->_rowClass(array(
            'row' => $proxyRow,
            'model' => $this
        ));
        return $ret;
    }

    public function checkCache ()
    {
        if (isset($this->_cacheData) && $this->_cacheData) return true;
        else return false;
    }

    public function afterInsert($row) {
        $this->_createCacheDataRow($row);
    }
    public function afterUpdate($row) {
        $this->_createCacheDataRow($row);
    }

    private function _createCacheDataRow($row)
    {
        foreach ($this->_cacheSettings as $cacheSetting) {
            //wenn cache nocht nicht geladen nicht updated
            if (!isset($this->_cacheData[$this->_getCacheId($cacheSetting)])) continue;
            $cachedata = array();
            $indexes = $cacheSetting['index'];
            $colindexes = $cacheSetting['columns'];
            $colindexes[] = $this->getPrimaryKey();
            $ret = array();
            $searchKey = $this->_getSearchKey($row, $indexes);
            $parts = array();
            foreach ($colindexes as $key => $colindex) {
                $parts['data'][$colindex] = $row->{$colindex};
            }
            $parts['id'] = $row->{$this->getPrimaryKey()};
            $this->_cacheData[$this->_getCacheId($cacheSetting)][$searchKey][$row->{$this->getPrimaryKey()}] = $parts;
        }
        $this->_rows[$row->{$this->getPrimaryKey()}] = $row;
    }

    public function deleteCacheDataRow($row)
    {
        foreach ($this->_cacheData as $index => $indexdata) {
            foreach ($indexdata as $searchkey => $rows) {
                unset($this->_cacheData[$index][$searchkey][$row->{$this->getPrimaryKey()}]);
            }
        }
    }
}
