<?php
/**
 * @package Model
 * @internal
 */
class Kwf_Model_Field_Row extends Kwf_Model_Row_Data_Abstract
{
    protected $_fieldName;
    protected $_siblingRow;

    /**
     * Beim speichern lässt es sich leider nicht anders rausfinden obs um
     * update oder insert geht, weil der Primary-key der Hauptrow da schon
     * gesetzt ist. Drum wird das im __construct() überprüft und in dieser
     * Variable gespeichert.
     */
    private $_isNewRow;

    public function __construct($config)
    {
        $this->_fieldName = $config['model']->getFieldName();
        $this->_siblingRow = $config['siblingRow'];
        $this->_isNewRow = empty($this->_siblingRow->{$this->_fieldName});
        parent::__construct($config);
    }

    public function getSiblingRow($rule = null)
    {
        return $this->_siblingRow;
    }

    protected function _beforeSaveSiblingMaster()
    {
        parent::_beforeSaveSiblingMaster();
        $this->_siblingRow->{$this->_fieldName} = json_encode($this->_data);
    }

    protected function _saveWithoutResetDirty()
    {
        $this->_beforeSaveSiblingMaster();
        $this->_beforeSave();
        if ($this->_isNewRow) {
            $this->_beforeInsert();
        } else {
            $this->_beforeUpdate();
        }

        Kwf_Model_Row_Abstract::_saveWithoutResetDirty();

        if ($this->_isNewRow) {
            $this->_afterInsert();
        } else {
            $this->_afterUpdate();
        }
        $this->_afterSave();

        $this->_isNewRow = false;
    }

    public function delete()
    {
        throw new Kwf_Exception("Can't delete sibling row");
    }
}
