<?php
/**
 * Iterator der Array-Export-Rows eines Models mit einem Select iteriert
 *
 * Sollte zusammen mit Kwf_Model_Iterator_Packages speichersparend sein
 *
 * @package Model
 * @subpackage Iterator
 */
class Kwf_Model_Iterator_ExportArray implements Kwf_Model_Iterator_ModelIterator_Interface
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
     * @var ArrayIterator
     **/
    private $_arrayIterator;

    /**
     * @var array
     **/
    private $_options;
    private $_debugOutput = false;

    public function __construct(Kwf_Model_Interface $model, Kwf_Model_Select $select, array $options = array(), $debugOutput = false)
    {
        $this->_model = $model;
        $this->_select = $select;
        $this->_options = $options;
        $this->_debugOutput = (bool)$debugOutput;
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
        $data = $this->_model->export(Kwf_Model_Interface::FORMAT_ARRAY,  $this->_select, $this->_options);
        if ($this->_debugOutput) {
            echo "loaded ".count($data)." rows";
            echo " memory: ".round(memory_get_usage()/1024/1024, 1)."MB\n";
        }
        $this->_arrayIterator = new ArrayIterator($data);
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
