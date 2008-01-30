<?php
class Vps_Auto_Vpc_Form extends Vps_Auto_Form
{
    private $_componentIdTemplate;

    public function __construct($class, $id = null)
    {
        $this->setProperty('class', $class);
        parent::__construct($class, $id);
    }

    public function setId($id)
    {
        $class = $this->getClass();

        $tablename = Vpc_Abstract::getSetting($class, 'tablename');
        if ($tablename) {
            $this->setTable(new $tablename(array('componentClass'=>$class)));
        } else {
            throw new Vpc_Exception('No tablename in Setting defined: ' . $class);
        }

        $table = $this->getTable();

        $this->_row = $table->find($id)->current();
        if (!$this->_row) {
            $this->_row = $table->createRow(array('component_id' => $id));
        }
        parent::setId($id);
    }
    public function setComponentIdTemplate($idTemplate)
    {
        $this->_componentIdTemplate = $idTemplate;
        return $this;
    }
    public function getComponentIdTemplate()
    {
        return $this->_componentIdTemplate;
    }

    protected function _getComponentIdFromParentRow($parentRow)
    {
        if (isset($this->_componentIdTemplate)) {
            return str_replace('{0}', $parentRow->id, $this->_componentIdTemplate);
        }
        throw new Vps_Exception("_getComponentIdFromParentRow has to be reimplemented or setComponentIdTemplate has to be set or the id has to be set");
    }

    public function delete($parentRow)
    {
        if ($this->getId() == null) {
            $this->setId($this->_getComponentIdFromParentRow($parentRow));
        }
        return parent::delete($parentRow);
    }
    public function load($parentRow)
    {
        if ($this->getId() == null) {
            $this->setId($this->_getComponentIdFromParentRow($parentRow));
        }
        return parent::load($parentRow);
    }
    public function prepareSave($parentRow, $postData)
    {
        if ($this->getId() == null && $parentRow->id) {
            $this->setId($this->_getComponentIdFromParentRow($parentRow));
        }
        return parent::prepareSave($parentRow, $postData);
    }

    public function save($parentRow, $postData)
    {
        $row = $this->getRow();
        if (!$row->component_id) {
            $id = $this->_getComponentIdFromParentRow($parentRow);
            $row->component_id = $id['component_id'];
        }
        return parent::save($parentRow, $postData);
    }
}
