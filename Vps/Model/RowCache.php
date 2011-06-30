<?php
class Vps_Model_RowCache extends Vps_Model_Proxy
{
    protected $_rowClass = 'Vps_Model_RowCache_Row';
    protected $_cacheColumns = array();

    private $_cacheRows = array();

    public function __construct(array $config = array())
    {
        if (isset($config['cacheColumns'])) $this->_cacheColumns = $config['cacheColumns'];
        parent::__construct($config);
    }

    public function getRowByProxiedRow($proxiedRow)
    {
        $id = $proxiedRow->{$this->getPrimaryKey()};
        if (isset($this->_cacheRows[$id])) {
            return $this->_cacheRows[$id];
        }
        return parent::getRowByProxiedRow($proxiedRow);
    }

    //aufgerufen von row beim speichern
    public function clearRowCache($id)
    {
        apc_delete($this->_getCacheId($id));
    }

    private function _getCacheId($id)
    {
        static $prefix;
        if (!isset($prefix)) $prefix = Vps_Cache::getUniquePrefix();
        return $prefix.'rowCache-'.$this->getUniqueIdentifier().'-'.$id;
    }

    public function getRow($select)
    {
        if (is_array($select) && count($select)==1 && isset($select['equals']) && count($select['equals'])==1 && isset($select['equals'][$this->getPrimaryKey()])) {
            $select = $select['equals'][$this->getPrimaryKey()];
        }
        if (!is_object($select) && !is_array($select)) {
            if (isset($this->_cacheRows[$select])) return $this->_cacheRows[$select];
            $cacheId = $this->_getCacheId($select);
            $success = false;
            $cacheData = apc_fetch($cacheId, $success);
            if (!$success) {
                $cacheData = array();
                $row = parent::getRow($select);
                if (!$row) return $row;
                $cacheData[$this->getPrimaryKey()] = $select;
                foreach ($this->_cacheColumns as $c) {
                    $cacheData[$c] = $row->$c;
                }
                apc_add($cacheId, $cacheData);
                $this->_cacheRows[$select] = $row;
                return $row;
            }
            $ret = new $this->_rowClass(array(
                'model' => $this,
                'cacheData' => $cacheData
            ));
            $this->_cacheRows[$select] = $ret;
            return $ret;
        } else {
            return parent::getRow($select);
        }
    }

    //wird nur von Row aufgerufen
    public function getSourceRowByIdForRow($id)
    {
        $ret = $this->getProxyModel()->getRow($id);
        return $ret;
    }

    public function getExistingRows()
    {
        $ret = parent::getExistingRows();
        foreach ($this->_cacheRows as $r) {
            if (!in_array($r, $ret, true)) $ret[] = $r;
        }
        return $ret;
    }

    public function clearRows()
    {
        parent::clearRows();
        $this->_cacheRows = array();
    }

    protected function _afterImport($format, $data, $options)
    {
        if ($format == self::FORMAT_ARRAY) {
            foreach ($data as $r) {
                $this->clearRowCache($r[$this->getPrimaryKey()]);
            }
        } else {
            apc_delete_file(new APCIterator('user', '#^'.preg_quote($this->_getCacheId('')).'#'));
        }
        parent::_afterImport($format, $data, $options);
    }

    protected function _afterDeleteRows($where)
    {
        parent::_afterDeleteRows($where);
        apc_delete_file(new APCIterator('user', '#^'.preg_quote($this->_getCacheId('')).'#'));
    }
}
