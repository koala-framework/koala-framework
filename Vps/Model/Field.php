<?php
class Vps_Model_Field extends Vps_Model_Abstract
{
    protected $_rowClass = 'Vps_Model_Field_Row';
    protected $_rowsetClass = 'Vps_Model_Field_Rowset';
    protected $_fieldName;
    protected $_parentModelName;
    protected $_parentModel;

    public function __construct(array $config = array())
    {
        if (isset($config['fieldName'])) {
            $this->_fieldName = $config['fieldName'];
        }
        if (isset($config['parentModelName'])) {
            $this->_parentModelName = $config['parentModelName'];
        }
        if (isset($config['parentModel'])) {
            $this->_parentModel = $config['parentModel'];
        }
        parent::__construct($config);
    }
    protected function _init()
    {
        parent::_init();
        if (!isset($this->_parentModel) && isset($this->_parentModelName)) {
            $this->_parentModel = new $this->_parentModelName();
        }
    }

    public function createRow(array $data=array())
    {
        $pk = $this->getPrimaryKey();
        $rowData = array();
        if (isset($data[$pk])) {
            $rowData = array($pk => $data[$pk]);
            unset($data[$pk]);
        }
        $rowData[$this->_fieldName] = serialize($data);
        $row = $this->_parentModel->createRow($rowData);
        return new $this->_rowClass(array(
            'model' => $this,
            'parentRow' => $row,
            'fieldName' => $this->_fieldName
        ));
    }
    public function find($id)
    {
        $rowset = $this->_parentModel->find($id);
        return new $this->_rowsetClass(array(
            'model' => $this,
            'parentRowset' => $rowset,
            'rowClass' => $this->_rowClass,
            'fieldName' => $this->_fieldName
        ));
    }

    public function fetchAll($where=null, $order=null, $limit=null, $start=null)
    {
        throw new Vps_Exception('fetchAll is not possible for Vps_Model_Field');
    }
    public function fetchCount(array $where = array())
    {
        throw new Vps_Exception('fetchCount is not possible for Vps_Model_Field');
    }

    public function getPrimaryKey()
    {
        return $this->_parentModel->getPrimaryKey();
    }

    public function getRowByParentRow($parentRow)
    {
        return new $this->_rowClass(array(
            'model' => $this,
            'parentRow' => $parentRow,
            'fieldName' => $this->_fieldName
        ));
    }
}
