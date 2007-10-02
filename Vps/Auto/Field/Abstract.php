<?php
abstract class Vps_Auto_Field_Abstract implements Vps_Collection_Item_Interface
{
    private $_properties;
    protected $_xtype = null;
    protected $_validators = array();
    private $_data;

    public function __construct($field_name = null, $field_label = null)
    {
        if ($field_name) $this->setProperty('name', $field_name);
        if ($field_label) $this->setProperty('fieldLabel', $field_label);
    }

    public function __call($method, $arguments)
    {
        if (substr($method, 0, 3) == 'set') {
            if (!isset($arguments[0])) {
                throw new Vps_Exception("Missing argument 1 (value)");
            }
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4);
            return $this->setProperty($name, $arguments[0]);
        } else if (substr($method, 0, 3) == 'get') {
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4);
            return $this->getProperty($name);
        } else {
            throw new Vps_Exception("Invalid method called: '$method'");
        }
    }

    public function setProperty($name, $value)
    {
        $this->_properties[$name] = $value;
        return $this;
    }

    public function getProperty($name)
    {
        if (isset($this->_properties[$name])) {
            return $this->_properties[$name];
        } else {
            return null;
        }
    }

    public function getMetaData()
    {
        $ret = $this->_properties;
        if (isset($ret['name'])) {
            $ret['name'] = $this->getFieldName();
        }
        if (isset($ret['hiddenName']) && $this->getNamePrefix()) {
            $ret['hiddenName'] = $this->getNamePrefix() . '_' . $ret['hiddenName'];
        }
        if (isset($ret['namePrefix'])) unset($ret['namePrefix']);
        if (!is_null($this->_xtype)) {
            $ret['xtype'] = $this->_xtype;
        }
        return $ret;
    }

    public function load($row)
    {
        $ret = array();
        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $field) {
                $ret = array_merge($ret, $field->load($row));
            }
        }
        return $ret;
    }

    public function prepareSave(Zend_Db_Table_Row_Abstract $row, $postData)
    {
        $this->_addValidators();

        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $field) {
                $field->prepareSave($row, $postData);
            }
        }
    }

    public function save(Zend_Db_Table_Row_Abstract $row)
    {
        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $field) {
                $field->save($row);
            }
        }
    }

    public function delete(Zend_Db_Table_Row_Abstract $row)
    {
        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $field) {
                $field->delete($row);
            }
        }
    }

    public function getName()
    {
        if (isset($this->_properties['name'])) {
            return $this->getProperty('name');
        } else if (isset($this->_properties['hiddenName'])) {
            return $this->getHiddenName();
        } else {
            return null;
        }
    }

    public function getFieldName()
    {
        $ret = $this->getName();
        if ($this->getNamePrefix()) {
            $ret = $this->getNamePrefix() . '_' . $ret;
        }
        return $ret;
    }

    public function getByName($name)
    {
        if ($this->getName() == $name) {
            return $this;
        } else {
            return null;
        }
    }

    public function hasChildren()
    {
        return false;
    }

    public function getChildren()
    {
        return array();
    }

    public function getValidators()
    {
        return $this->_validators;
    }
    public function addValidator(Zend_Validate_Interface $v)
    {
        $this->_validators[] = $v;
    }

    /**
     * Fügt die Standard-Validatoren für dieses Feld hinzu.
     * wird aufgerufen in prepareSave
    **/
    protected function _addValidators()
    {
    }

    public function getData()
    {
        if (!isset($this->_data)) {
            $this->setData(new Vps_Auto_Data_Table());
        }
        return $this->_data;
    }

    public function setData(Vps_Auto_Data_Interface $data)
    {
        $this->_data = $data;
        $data->setFieldname($this->getName());
        return $this;
    }
}
