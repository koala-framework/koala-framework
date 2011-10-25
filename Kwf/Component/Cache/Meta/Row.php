<?php
class Kwf_Component_Cache_Meta_Row extends Kwf_Component_Cache_Meta_ModelField
{
    public function __construct($row)
    {
        if ($row instanceof Kwf_Model_Row_Abstract) {
            $model = $row->getModel();
        } else if ($row instanceof Zend_Db_Table_Row_Abstract) {
            $model = $row->getTable();
        } else {
            throw new Kwf_Exception('Row must be instance of Kwf_Model_Row_Abstract or Zend_Db_Table_Row_Abstract');
        }
        $model = $this->_getModel($model);
        $column = $model->getPrimaryKey();
        $value = $row->$column;
        parent::__construct($model, $column, $value);
    }
}