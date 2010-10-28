<?php
class Vps_Model_RowsSubModel_Proxy extends Vps_Model_Proxy
    implements Vps_Model_RowsSubModel_Interface
{
    protected $_rowClass = 'Vps_Model_RowsSubModel_Proxy_Row';
    /**
     * @var Vps_Model_Interface
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
        if (!$this->_proxyModel instanceof Vps_Model_RowsSubModel_Interface) {
            throw new Vps_Exception("proxyModel doesn't implement Vps_Model_RowsSubModel_Interface");
        }
        if ($this->_parentModel) $this->_proxyModel->setParentModel($this->_parentModel);
    }

    public function setParentModel(Vps_Model_Interface $m)
    {
        $this->_parentModel = $m;
        $this->_proxyModel->setParentModel($m);
    }

    public function createRowByParentRow(Vps_Model_Row_Interface $parentRow, array $data = array())
    {
        while ($parentRow instanceof Vps_Model_Proxy_Row) $parentRow = $parentRow->getProxiedRow();
        $proxyRow = $this->_proxyModel->createRowByParentRow($parentRow);
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

    public function getRowsByParentRow(Vps_Model_Row_Interface $parentRow, $select = array())
    {
        while ($parentRow instanceof Vps_Model_Proxy_Row) $parentRow = $parentRow->getProxiedRow();
        $proxyRowset = $this->_proxyModel->getRowsByParentRow($parentRow, $select);
        return new $this->_rowsetClass(array(
            'rowset' => $proxyRowset,
            'rowClass' => $this->_rowClass,
            'model' => $this,
        ));
    }

    public function createRow(array $data=array())
    {
        throw new Vps_Exception('getRows is not possible for Vps_Model_Mongo_RowsSubModel');
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        throw new Vps_Exception('getRows is not possible for Vps_Model_Mongo_RowsSubModel');
    }
}
