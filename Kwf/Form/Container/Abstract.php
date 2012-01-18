<?php
/**
 * Basisklasse für Fields die andere Fields beinhalten
 *
 * @package Form
 **/
abstract class Kwf_Form_Container_Abstract extends Kwf_Form_Field_Abstract
    implements IteratorAggregate
{
    /**
     * @var Kwf_Collection_FormFields
     */
    public $fields;

    public function __construct($name = null)
    {
        parent::__construct($name);
        if (!isset($this->fields)) {
            $this->fields = new Kwf_Collection_FormFields();
        }
        $this->setLayout('form');
        $this->setBorder(false);
        $this->setLabelAlign('right');
        $this->setBaseCls('x-plain');
    }

    /**
     * @internal
     */
    public function __clone()
    {
        $this->fields = clone $this->fields;
    }

    protected function _init()
    {
        parent::_init();
        $this->_initFields();
    }

    protected function _initFields(){}

    public function getMetaData($model)
    {
        $iterator = new RecursiveIteratorIterator(new Kwf_Collection_Iterator_Recursive($this->fields));
        foreach ($iterator as $field) {
            if ($field->getLoadAfterSave()) {
                $ret['loadAfterSave'] = true;
            }
        }
        $ret = parent::getMetaData($model);
        $ret['items'] = $this->fields->getMetaData($model);
        if (!count($ret['items'])) unset($ret['items']);
        return $ret;
    }

    public function getByName($name)
    {
        $ret = parent::getByName($name);
        if($ret) return $ret;
        return $this->fields->getByName($name);
    }

    public function hasChildren()
    {
        return sizeof($this->fields) > 0;
    }
    public function getChildren()
    {
        return $this->fields;
    }

    public function add($v = null)
    {
        return $this->fields->add($v);
    }

    public function prepend($v)
    {
        return $this->fields->prepend($v);
    }

    public function insertBefore($where, $v = null)
    {
        return $this->fields->insertBefore($where, $v);
    }
    public function insertAfter($where, $v = null)
    {
        return $this->fields->insertAfter($where, $v);
    }

    //IteratorAggregate
    public function getIterator()
    {
        return $this->fields->getIterator();
    }

    public function setNamePrefix($v)
    {
        if ($this->getName()) {
            $v = $v . '_' . $this->getName();
        }
        $this->fields->setFormName($v);
        return $this;
    }

    //kann überschrieben werden wenn wir eine anderen row haben wollen
    protected function _getRowByParentRow($parentRow)
    {
        return $parentRow;
    }

    public function prepareSave($parentRow, $postData)
    {
        $row = $this->_getRowByParentRow($parentRow);
        parent::prepareSave($row, $postData);
    }

    public function save($parentRow, $postData)
    {
        if ($this->getSave() === false) return array();

        $row = $this->_getRowByParentRow($parentRow);
        parent::save($row, $postData);
    }

    public function afterSave($parentRow, $postData)
    {
        //wenn form zB in einem CardLayout liegt und deaktivert wurde nicht speichern
        if ($this->getSave() === false) return array();

        $row = $this->_getRowByParentRow($parentRow);
        parent::afterSave($row, $postData);
    }

    public function delete($parentRow)
    {
        $row = $this->_getRowByParentRow($parentRow);
        parent::delete($row);
    }

    public function load($parentRow, $postData = array())
    {
        $row = $this->_getRowByParentRow($parentRow);
        return parent::load($row, $postData);
    }

    public function validate($parentRow, $postData = array())
    {
        $row = $this->_getRowByParentRow($parentRow);
        return parent::validate($row, $postData);
    }

    public function processInput($parentRow, $postData = array())
    {
        $row = $this->_getRowByParentRow($parentRow);
        return parent::processInput($row, $postData);
    }

    public function getTemplateVars($values, $fieldNamePostfix='', $idPrefix='')
    {
        $ret = parent::getTemplateVars($values, $fieldNamePostfix, $idPrefix);
        $ret['items'] = $this->fields->getTemplateVars($values, $fieldNamePostfix, $idPrefix);
        return $ret;
    }
}
