<?php
/**
 * Iterator der Rows eines Models mit einem Select iteriert
 *
 * könnte womöglich in normales rowset integiert werden
 *
 * @package Model
 * @subpackage Iterator
 */
class Kwf_Model_Iterator_Rows implements Kwf_Model_Iterator_ModelIterator_Interface
{
    /**
     * @var Kwf_Model_Interface
     **/
    private $_model;

    /**
     * @var Kwf_Model_Select
     **/
    private $_select;

    /**
     * @var Kwf_Model_Rowset_Interface
     **/
    private $_rowset;

    public function __construct(Kwf_Model_Interface $model, Kwf_Model_Select $select)
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
        $this->_rowset = $this->_model->getRows($this->_select);
    }

    public function current()
    {
        return $this->_rowset->current();
    }

    public function key()
    {
        return $this->_rowset->key();
    }

    public function next()
    {
        $this->_rowset->next();
    }

    public function valid()
    {
        return $this->_rowset->valid();
    }
}
