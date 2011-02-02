<?php
/**
 * Iterator der Array-Export-Rows eines Models mit einem Select iteriert
 *
 * Sollte zusammen mit Vps_Model_Iterator_Packages speichersparend sein
 */
class Vps_Model_Iterator_ExportArray implements Vps_Model_Iterator_ModelIterator_Interface
{
    /**
     * @var Vps_Model_Interface
     **/
    private $_model;

    /**
     * @var Vps_Model_Select
     **/
    private $_select;

    /**
     * @var ArrayIterator
     **/
    private $_arrayIterator;

    public function __construct(Vps_Model_Interface $model, Vps_Model_Select $select)
    {
        $this->_model = $model;
        $this->_select = $select;
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function getSelect()
    {
        return $this->_select;
    }

    public function rewind()
    {
        $this->_arrayIterator = new ArrayIterator($this->_model->export(Vps_Model_Interface::FORMAT_ARRAY,  $this->_select));
    }

    public function current()
    {
        return $this->_arrayIterator->current();
    }

    public function key()
    {
        return $this->_arrayIterator->key();
    }

    public function next()
    {
        $this->_arrayIterator->next();
    }

    public function valid()
    {
        return $this->_arrayIterator->valid();
    }
}
