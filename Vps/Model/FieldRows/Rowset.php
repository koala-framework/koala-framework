<?php
class Vps_Model_FieldRows_Rowset extends Vps_Model_Rowset_Abstract
{
    protected $_parentRow;
    public function __construct($config)
    {
        $this->_parentRow = $config['parentRow'];
        parent::__construct($config);
    }
    protected function _getRowConfig($index)
    {
        $ret = parent::_getRowConfig($index);
        $ret['parentRow'] = $this->_parentRow;
        return $ret;
    }
}
