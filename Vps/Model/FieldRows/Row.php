<?php
class Vps_Model_FieldRows_Row extends Vps_Model_Row_Data_Abstract implements Vps_Model_RowsSubModel_Row_Interface
{
    protected $_parentRow;
    public function __construct(array $config)
    {
        $this->_parentRow = $config['parentRow'];
        parent::__construct($config);
    }

    public function getSubModelParentRow()
    {
        return $this->_parentRow;
    }
}
