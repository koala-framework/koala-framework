<?php
class Vps_Model_Rowset_ParentRow extends Vps_Model_Rowset_Abstract
{
    protected $_parentRow;
    public function __construct($config)
    {
        $this->_parentRow = $config['parentRow'];
        parent::__construct($config);
    }

    protected function _getRowByDataKey($key)
    {
        return $this->getModel()->getRowByDataKey($key, $this->_parentRow);
    }
}
