<?php
/**
 * @package Model
 */
class Kwf_Model_Proxy extends Kwf_Model_Abstract
{
    /**
     * @var Kwf_Model_Interface
     */
    protected $_proxyModel;
    protected $_rowClass = 'Kwf_Model_Proxy_Row';
    protected $_rowsetClass = 'Kwf_Model_Proxy_Rowset';

    public function __construct(array $config = array())
    {
        if (isset($config['proxyModel'])) $this->_proxyModel = $config['proxyModel'];
        parent::__construct($config);
    }

    protected function _init()
    {
        parent::_init();
        if (!$this->_proxyModel) {
            throw new Kwf_Exception("proxyModel config is required for model '".get_class($this)."'");
        }
        if (!is_string($this->_proxyModel)) {
            $this->_proxyModel->addProxyContainerModel($this);
        }
    }

    //kann gesetzt werden von proxy (rekursiv bei proxys)
    public function addProxyContainerModel($m)
    {
        parent::addProxyContainerModel($m);
        $this->getProxyModel()->addProxyContainerModel($m);
    }

    public function getProxyModel()
    {
        if (is_string($this->_proxyModel)) {
            $this->_proxyModel = Kwf_Model_Abstract::getInstance($this->_proxyModel);
            $this->_proxyModel->addProxyContainerModel($this);
        }
        return $this->_proxyModel;
    }

    public function createRow(array $data=array())
    {
        $proxyRow = $this->getProxyModel()->createRow();
        $ret = new $this->_rowClass(array(
            'row' => $proxyRow,
            'model' => $this
        ));
        $this->_rows[$proxyRow->getInternalId()] = $ret;
        $data = array_merge($this->_default, $data);
        foreach ($data as $k=>$i) {
            $ret->$k = $i;
        }
        return $ret;
    }

    public function getRowByProxiedRow($proxiedRow)
    {
        $id = $proxiedRow->getInternalId();
        if (!isset($this->_rows[$id])) {
            $this->_rows[$id] = new $this->_rowClass(array(
                'row' => $proxiedRow,
                'model' => $this,
                'exprValues' => $this->_getExprValues($proxiedRow)
            ));
        }
        return $this->_rows[$id];
    }

    protected function _getExprValues($proxiedRow)
    {
        $exprValues = array();
        if ($this->_exprs) {
            $r = $proxiedRow;
            while ($r instanceof Kwf_Model_Proxy_Row) {
                $r = $r->getProxiedRow();
            }
            if ($r instanceof Kwf_Model_Db_Row) {
                $r = $r->getRow();
                foreach (array_keys($this->_exprs) as $k) {
                    if (isset($r->$k)) {
                        $exprValues[$k] = $r->$k;
                    }
                }
            }
        }
        return $exprValues;
    }

    public function getPrimaryKey()
    {
        return $this->getProxyModel()->getPrimaryKey();
    }

    public function isEqual(Kwf_Model_Interface $other)
    {
        if ($this->getProxyModel()->isEqual($other)) return true;
        if ($other instanceof Kwf_Model_Proxy) {
            return $this->getProxyModel()->isEqual($other->getProxyModel());
        }
        return false;
    }

    public function getColumnType($col)
    {
        if ($this->getProxyModel()->hasColumn($col)) {
            return $this->getProxyModel()->getColumnType($col);
        }
        return parent::getColumnType($col);
    }

    protected function _getOwnColumns()
    {
        return $this->getProxyModel()->getColumns();
    }

    public function hasColumn($col)
    {
        if ($this->getProxyModel()->hasColumn($col)) return true;
        if (in_array($col, $this->getExprColumns())) return true;
        foreach ($this->getSiblingModels() as $m) {
            if ($m->hasColumn($col)) return true;
        }
        return false;
    }

    public function getExprColumns()
    {
        $ret = parent::getExprColumns();
        $ret = array_merge($this->getProxyModel()->getExprColumns(), $ret);
        return $ret;
    }

    public function getIds($where=null, $order=null, $limit=null, $start=null)
    {
        return $this->getProxyModel()->getIds($where, $order, $limit, $start);
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        $proxyRowset = $this->getProxyModel()->getRows($where, $order, $limit, $start);
        return new $this->_rowsetClass(array(
            'rowset' => $proxyRowset,
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }

    public function getRow($select)
    {
        if (!$select) {
            throw new Kwf_Exception('getRow needs a parameter, null is not allowed.');
        }
        $proxyRow = $this->getProxyModel()->getRow($select);
        if (!$proxyRow) return $proxyRow;
        return $this->getRowByProxiedRow($proxyRow);
    }

    public function deleteRows($where)
    {
        Kwf_Component_ModelObserver::getInstance()->disable();
        $ret = $this->getProxyModel()->deleteRows($where);
        Kwf_Component_ModelObserver::getInstance()->enable();
        return $ret;
    }

    public function updateRows($data, $where)
    {
        Kwf_Component_ModelObserver::getInstance()->disable();
        $ret = $this->getProxyModel()->updateRows($data, $where);
        Kwf_Component_ModelObserver::getInstance()->enable();
        return $ret;
    }

    public function countRows($where = array())
    {
        return $this->getProxyModel()->countRows($where);
    }

    public function getUniqueIdentifier() {
        return $this->getProxyModel()->getUniqueIdentifier().'_proxy';
    }

    public function getSupportedImportExportFormats()
    {
        return $this->getProxyModel()->getSupportedImportExportFormats();
    }

    public function export($format, $select = array(), $options = array())
    {
        return $this->getProxyModel()->export($format, $select, $options);
    }

    public function import($format, $data, $options = array())
    {
        if (!isset($options['skipModelObserver']) || !$options['skipModelObserver']) {
            Kwf_Component_ModelObserver::getInstance()->disable();
        }
        $this->getProxyModel()->import($format, $data, $options);
        if (!isset($options['skipModelObserver']) || !$options['skipModelObserver']) {
            Kwf_Component_ModelObserver::getInstance()->enable();
        }
    }

    public function writeBuffer()
    {
        $this->getProxyModel()->writeBuffer();
    }

    public function getTable()
    {
        return $this->getProxyModel()->getTable();
    }

    public function getSqlForSelect($select)
    {
        return $this->getProxyModel()->getSqlForSelect($select);
    }

    public function dependentModelRowUpdated(Kwf_Model_Row_Abstract $row, $action)
    {
        parent::dependentModelRowUpdated($row, $action);
        $this->getProxyModel()->dependentModelRowUpdated($row, $action);
    }

    public function childModelRowUpdated(Kwf_Model_Row_Abstract $row, $action)
    {
        parent::childModelRowUpdated($row, $action);
        $this->getProxyModel()->childModelRowUpdated($row, $action);
    }

    /**
     * @internal
     */
    public function getExistingRows()
    {
        return $this->_rows;
    }

    public function fetchColumnByPrimaryId($column, $id)
    {
        if ($this->getProxyModel()->hasColumn($column)) {
            return $this->getProxyModel()->fetchColumnByPrimaryId($column, $id);
        } else {
            return parent::fetchColumnByPrimaryId($column, $id);
        }
    }

    public function fetchColumnsByPrimaryId(array $columns, $id)
    {
        return $this->getProxyModel()->fetchColumnsByPrimaryId($columns, $id);
    }

    public function afterInsert($row)
    {
        $this->getProxyModel()->afterInsert($row);
    }
}
