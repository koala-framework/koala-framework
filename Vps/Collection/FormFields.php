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

    public function getMetaData($model)
    {
        $ret = array();
        foreach ($this as $field) {
            $data = $field->getMetaData($model);
            if (!is_null($data)) $ret[] = $data;
        }
        return $ret;
    }

    public function getTemplateVars($values, $fieldNamePostfix = '')
    {
        $ret = array();
        foreach ($this as $field) {
            if ($field->getHidden()) continue;
            $data = $field->getTemplateVars($values, $fieldNamePostfix);
            if (!is_null($data)) $ret[] = $data;
        }
        return $ret;
    }
}
