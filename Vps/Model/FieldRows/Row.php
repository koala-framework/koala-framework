<?php
class Vps_Model_FieldRows_Row extends Vps_Model_Row_Data_Abstract
{
    protected $_parentRow;
    public function __construct(array $config)
    {
        $this->_parentRow = $config['parentRow'];
        parent::__construct($config);
    }

    public function getModelParentRow()
    {
        return $this->_parentRow;
    }

    protected function _refresh($id)
    {
        $select = new Vps_Model_Select();
        $select->whereId($id);
        $this->_data = $this->_model->getRowsByParentRow($this->_parentRow, $select)->current()->_data;
        $this->_cleanData = $this->_data;
    }

}
