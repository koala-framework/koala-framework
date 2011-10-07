<?php
class Kwf_Model_RowsSubModel_Proxy extends Kwf_Model_Proxy
    implements Kwf_Model_RowsSubModel_Interface
{
    protected $_rowClass = 'Kwf_Model_RowsSubModel_Proxy_Row';
    /**
     * @var Kwf_Model_Interface
     */
    protected $_parentModel;

    public function __construct(array $config = array())
    {
        if (isset($config['parentModel'])) {
            $this->_parentModel = $config['parentModel'];
        }
        parent::__construct($config);
    }

    protected function _init()
    {
        parent::_init();
        if (!$this->getProxyModel() instanceof Kwf_Model_RowsSubModel_Interface) {
            throw new Kwf_Exception("proxyModel doesn't implement Kwf_Model_RowsSubModel_Interface");
        }
        if ($this->_parentModel) $this->getProxyModel()->setParentModel($this->_parentModel);
    }

    public function setParentModel(Kwf_Model_Interface $m)
    {
        $this->_parentModel = $m;
        $this->getProxyModel()->setParentModel($m);
    }

    public function getParentModel()
    {
        return $this->_parentModel;
    }

    public function createRowByParentRow(Kwf_Model_Row_Interface $parentRow, array $data = array())
    {
        while ($parentRow instanceof Kwf_Model_Proxy_Row) $parentRow = $parentRow->getProxiedRow();
        $proxyRow = $this->getProxyModel()->createRowByParentRow($parentRow);
        $ret = new $this->_rowClass(array(
            'row' => $proxyRow,
            'model' => $this,
        ));
        $this->_rows[$proxyRow->getInternalId()] = $ret;
        $data = array_merge($this->_default, $data);
        foreach ($data as $k=>$i) {
            $ret->$k = $i;
        }
        return $ret;
    }

    public function getRowsByParentRow(Kwf_Model_Row_Interface $parentRow, $select = array())
    {
        while ($parentRow instanceof Kwf_Model_Proxy_Row) $parentRow = $parentRow->getProxiedRow();
        $proxyRowset = $this->getProxyModel()->getRowsByParentRow($parentRow, $select);
        return new $this->_rowsetClass(array(
            'rowset' => $proxyRowset,
            'rowClass' => $this->_rowClass,
            'model' => $this,
        ));
    }

    public function createRow(array $data=array())
    {
        throw new Kwf_Exception('getRows is not possible for Kwf_Model_Mongo_RowsSubModel');
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        throw new Kwf_Exception('getRows is not possible for Kwf_Model_Mongo_RowsSubModel');
    }
}
