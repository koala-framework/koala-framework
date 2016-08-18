<?php
/**
 * Filters a field using another field. Can be used with two Select fields.
 *
 * TODO: support for non-remote stores
 */
class Kwf_Form_Field_FilterField extends Kwf_Form_Field_Abstract
{
    private $_namePrefix;
    public function __construct()
    {
        parent::__construct();
        $this->setXtype('filterfield');
        $this->setFilterParam('filter');
    }

    /**
     * Sets the name prefix for the filter/filtered fields
     */
    public function setNamePrefix($name)
    {
        $this->_namePrefix = $name;
        if ($this->getFilterField()) $this->getFilterField()->setNamePrefix($name);
        if ($this->getFilteredField()) $this->getFilteredField()->setNamePrefix($name);
        return $this;
    }

    /**
     * Sets the filter field (the one that filters the filtered field)
     */
    public function setFilterField(Kwf_Form_Field_Abstract $field)
    {
        $property = $this->setProperty('filterField', $field);
        $field->setNamePrefix($this->_namePrefix);
        return $property;
    }

    /**
     * Sets the filtered field (the one that's filtered by filter field)
     */
    public function setFilteredField(Kwf_Form_Field_Abstract $field)
    {
        $property = $this->setProperty('filteredField', $field);
        $field->setNamePrefix($this->_namePrefix);
        return $property;
    }

    /**
     * Sets the name of the used parameter for the filtered query
     *
     * defaults to 'filter'
     *
     * @param string
     */
    public function setFilterColumn($v)
    {
        return $this->setProperty('filterColumn', $v);
    }

    public function getFilteredField()
    {
        return $this->getProperty('filteredField');
    }

    public function getFilterField()
    {
        return $this->getProperty('filterField');
    }

    public function getFilterColumn()
    {
        return $this->getProperty('filterColumn');
    }

    public function hasChildren()
    {
        return true;
    }

    public function getChildren()
    {
        if (!$this->getFilterField()) {
            throw new Kwf_Exception("filterField is required");
        }
        if (!$this->getFilteredField()) {
            throw new Kwf_Exception("filteredField is required");
        }
        return array(
            $this->getFilterField(),
            $this->getFilteredField(),
        );
    }

    public function load($row, $postData = array())
    {
        $ret = parent::load($row, $postData);
        return $ret;
    }

    public function getMetaData($model)
    {
        $ret = parent::getMetaData($model);
        $ret['items'] = array(
            $this->getFilterField()->getMetaData($model),
            $this->getFilteredField()->getMetaData($model),
        );
        return $ret;
    }
}
