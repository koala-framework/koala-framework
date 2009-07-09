<?php
class Vps_Db_TableFieldsModel_Row extends Vps_Model_Row_Data_Abstract
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

    protected function _callObserver($fn)
    {
    }
}
