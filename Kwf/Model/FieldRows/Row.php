<?php
/**
 * @package Model
 * @internal
 */
class Kwf_Model_FieldRows_Row extends Kwf_Model_Row_Data_Abstract implements Kwf_Model_RowsSubModel_Row_Interface
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
