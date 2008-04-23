<?php
class Vps_Collection_FormFields extends Vps_Collection
{
    private $_formName = null;

    public function __construct($name = null, $defaultClass = null)
    {
        $this->_formName = $name;
        parent::__construct($defaultClass);
    }

    protected function _postInsertValue($field)
    {
        if ($this->_formName) {
            $field->setNamePrefix($this->_formName);
        }
    }

    public function setFormName($name)
    {
        $this->_formName = $name;
        foreach ($this as $field) {
            $this->_postInsertValue($field);
        }
    }

    public function getFormName()
    {
        return $this->_formName;
    }

    public function getMetaData()
    {
        $ret = array();
        foreach ($this as $field) {
            $data = $field->getMetaData();
            if (!is_null($data)) $ret[] = $data;
        }
        return $ret;
    }

    public function getTemplateVars($values)
    {
        $ret = array();
        foreach ($this as $field) {
            $data = $field->getTemplateVars($values);
            if (!is_null($data)) $ret[] = $data;
        }
        return $ret;
    }
}
