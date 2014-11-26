<?php
/**
 * @package Model
 */
class Kwf_Model_RowCache extends Kwf_Model_Proxy
{
    protected $_rowClass = 'Kwf_Model_RowCache_Row';
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

    //called by Events when saving
    public function clearRowCache($id)
    {
        Kwf_Cache_Simple::delete($this->_getCacheId($id));
    }

    //called by Events when saving
    public function clearRowsCache($select)
    {
        $cacheIds = array();
        foreach ($this->getIds($select) as $id) {
            $cacheIds[] = $this->_getCacheId($id);
        }
        Kwf_Cache_Simple::delete($cacheIds);
    }

    private function _getCacheId($id)
    {
        return 'rowCache-'.$this->getUniqueIdentifier().'-'.$id;
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
            $cacheData = Kwf_Cache_Simple::fetch($cacheId, $success);
            if (!$success) {
                $cacheData = array();
                $row = parent::getRow($select);
                if (!$row) return $row;
                $cacheData[$this->getPrimaryKey()] = $select;
                foreach ($this->_cacheColumns as $c) {
                    $cacheData[$c] = $row->$c;
                }
                Kwf_Cache_Simple::add($cacheId, $cacheData);
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

    public function freeMemory()
    {
        foreach ($this->_cacheRows as $row) {
            if (is_object($row)) $row->freeMemory();
        }
        $this->_cacheRows = array();
    }

    protected function _afterDeleteRows($where)
    {
        parent::_afterDeleteRows($where);
        $cacheIds = array();
        foreach ($this->getIds($where) as $id) {
            $cacheIds[] = $this->_getCacheId($id);
        }
        Kwf_Cache_Simple::delete($cacheIds);
    }

    public function getEventSubscribers()
    {
        $ret = parent::getEventSubscribers();
        $ret[] = Kwf_Model_EventSubscriber::getInstance('Kwf_Model_RowCache_Events', array(
            'modelFactoryConfig' => $this->getFactoryConfig()
        ));
        return $ret;
    }
}
