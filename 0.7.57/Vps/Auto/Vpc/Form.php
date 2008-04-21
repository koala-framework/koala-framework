<?php
class Vps_Auto_Vpc_Form extends Vps_Auto_Form
{
    private $_componentIdTemplate;

    public function __construct($name, $class, $id = null)
    {
        $this->setProperty('class', $class);
        parent::__construct($name, $id);
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
            if (isset($parentRow->component_id)) {
                $id = $parentRow->component_id;
            } else {
                $id = $parentRow->id;
            }
            if (!$id) return null;
            return str_replace('{0}', $id, $this->_componentIdTemplate);
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
        if ($this->getId() == null) {
            $this->setId($this->_getComponentIdFromParentRow($parentRow));
        }
        return parent::prepareSave($parentRow, $postData);
    }

    public function save($parentRow, $postData)
    {
        $row = $this->getRow();
        $primaryKey = $this->getPrimaryKey();
        if (!$row->$primaryKey) {
            $id = $this->_getComponentIdFromParentRow($parentRow);
            $row->$primaryKey = $id;
        }
        return parent::save($parentRow, $postData);
    }

    public static function createComponentForm($name, $class)
    {
        $f = Vpc_Admin::getComponentClass($class, 'Form');
        return new $f($name, $class);
    }
}
