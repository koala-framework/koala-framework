<?php
class Vps_Model_Field_Row extends Vps_Model_Row_Abstract
{
    protected $_fieldName;
    protected $_parentRow;

    public function __construct($config)
    {
        $pk = $config['model']->getPrimaryKey();

        $this->_fieldName = $config['fieldName'];
        $this->_parentRow = $config['parentRow'];
        $data = unserialize($this->_parentRow->{$this->_fieldName});
        $data[$pk] = $this->_parentRow->$pk;
        $config['data'] = $data;
        parent::__construct($config);
    }

    public function __set($name, $value)
    {
        parent::__set($name, $value);
        $pk = $this->_getPrimaryKey();

        $data = $this->_data;
        unset($data[$pk]);
        $this->_parentRow->{$this->_fieldName} = serialize($data);
    }

    public function save()
    {
        $this->_parentRow->save();
    }

    public function delete()
    {
        $this->_parentRow->delete();
    }
}
