<?php
/**
 * Basisklasse für Fields die andere Fields beinhalten
 *
 **/
abstract class Vps_Form_Container_Abstract extends Vps_Form_Field_Abstract implements IteratorAggregate
{
    public $fields;

    public function __construct($name = null)
    {
        parent::__construct($name);
        if (!isset($this->fields)) {
            $this->fields = new Vps_Collection_FormFields();
        }
        $this->setLayout('form');
        $this->setBorder(false);
        $this->setLabelAlign('right');
        $this->setBaseCls('x-plain');
    }

    public function getMetaData()
    {
        $iterator = new RecursiveIteratorIterator(new Vps_Collection_Iterator_Recursive($this->fields));
        foreach ($iterator as $field) {
            if ($field instanceof Vps_Form_Field_HtmlEditor) {
                $this->setLoadAfterSave(true);
            }
        }
        $ret = parent::getMetaData();
        $ret['items'] = $this->fields->getMetaData();
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

    //IteratorAggregate
    public function getIterator()
    {
        return $this->fields->getIterator();
    }

    public function setSave($v)
    {
        $this->setProperty('save', $v);
        foreach ($this as $f) {
            $f->setSave($v);
        }
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
        //wenn form zB in einem CardLayout liegt und deaktivert wurde nicht speichern
        if ($this->getSave() === false) return array();

        $row = $this->_getRowByParentRow($parentRow);
        parent::save($row, $postData);
    }

    public function delete($parentRow)
    {
        $row = (object)$this->_getRowByParentRow($parentRow);
        parent::delete($row);
    }

    public function load($parentRow)
    {
        $row = (object)$this->_getRowByParentRow($parentRow);
        return parent::load($row);
    }

    public function getTemplateVars($values)
    {
        $ret = parent::getTemplateVars($values);
        $ret['items'] = $this->fields->getTemplateVars($values);
        return $ret;
    }
}
