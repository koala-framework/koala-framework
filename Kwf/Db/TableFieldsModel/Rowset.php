<?php
class Kwf_Db_TableFieldsModel_Rowset extends Kwf_Model_Rowset_Abstract
{
    protected $_parentRow;
    public function __construct($config)
    {
        $this->_parentRow = $config['parentRow'];
        parent::__construct($config);
    }

    public function current()
    {
        if ($this->valid() === false) {
            return null;
        }
        $key = $this->_dataKeys[$this->_pointer];
        return $this->getModel()->getRowByDataKeyAndParentRow($key, $this->_parentRow);
    }
}
