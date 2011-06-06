<?php
class Vps_Model_Field_Row extends Vps_Model_Row_Data_Abstract
{
    protected $_fieldName;
    protected $_siblingRow;

    public function __construct($config)
    {
        $this->_fieldName = $config['model']->getFieldName();
        $this->_siblingRow = $config['siblingRow'];
        parent::__construct($config);
    }

    public function getSiblingRow()
    {
        return $this->_siblingRow;
    }

    protected function _beforeSaveSiblingMaster()
    {
        parent::_beforeSaveSiblingMaster();
        $this->_siblingRow->{$this->_fieldName} = json_encode($this->_data);
    }

    public function save()
    {
        $update = isset($this->_cleanData[$this->_getPrimaryKey()]);

        $this->_beforeSaveSiblingMaster();
        $this->_beforeSave();
        if ($update) {
            $this->_beforeUpdate();
        } else {
            $this->_beforeInsert();
        }

        Vps_Model_Row_Abstract::save();

        if ($update) {
            $this->_afterUpdate();
        } else {
            $this->_afterInsert();
        }
        $this->_afterSave();
    }

    public function delete()
    {
        throw new Vps_Exception("Can't delete sibling row");
    }
}
