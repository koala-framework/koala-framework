<?php
class Vps_Model_Field_Rowset implements Vps_Model_Rowset_Interface
{
    protected $_fieldName;
    protected $_parentRowset;
    protected $_rowClass;
    protected $_model;

    public function __construct(array $config)
    {
        $this->_fieldName = $config['fieldName'];
        $this->_parentRowset = $config['parentRowset'];
        $this->_rowClass = $config['rowClass'];
        $this->_model = $config['model'];
    }

    public function rewind()
    {
        $this->_parentRowset->rewind();
        return $this;
    }

    public function current()
    {
        $row = $this->_parentRowset->current();
        if (is_null($row)) return null;
        return new $this->_rowClass(array(
            'parentRow' => $row,
            'model' => $this->_model,
            'fieldName' => $this->_fieldName
        ));
    }

    public function key()
    {
        return $this->_parentRowset->key();
    }

    public function next()
    {
        $this->_parentRowset->next();
    }

    public function valid()
    {
        return $this->_parentRowset->valid();
    }

    public function count()
    {
        return $this->_parentRowset->count();
    }
    public function seek($position)
    {
        $this->_parentRowset->seek($position);
        return $this;
    }
}
