<?php
abstract class Vps_Controller_Action_Auto_Form extends Vps_Controller_Action_Auto_Abstract
{
    protected $_form = null;
    protected $_fields = array(); //deprecated
    protected $_buttons = array('save' => true);

    public function indexAction()
    {
       $this->view->ext('Vps.Auto.FormPanel', $this->_form->getProperties());
    }

    public function jsonIndexAction()
    {
       $this->indexAction();
    }

    protected function _initFields()
    {
    }

    public function preDispatch()
    {
        $this->_form = new Vps_Auto_Form();

        foreach ($this->_fields as $k=>$field) {
            if (!isset($field['type'])) throw new Vps_Exception("no type for field no $k specified");
            $cls = 'Vps_Auto_Field_'.$field['type'];
            if (!class_exists($cls)) throw new Vps_Exception("Invalid type: Form-Field-Class $cls does not exist.");
            $fieldObject = new $cls();
            unset($field['type']);
            foreach ($field as $propName => $propValue) {
                $fieldObject->setProperty($propName, $propValue);
            }
            $this->_form->fields[] = $fieldObject;
        }
        if (!$this->_form->getTable()) {
            if (isset($this->_table)) {
                $this->_form->setTable($this->_table);
            } else if (isset($this->_tableName)) {
                $this->_form->setTable(new $this->_tableName);
            }
        }

        $this->_initFields();

        if (is_array($this->_form->getPrimaryKey())) {
            foreach ($this->_form->getPrimaryKey() as $key) {
                $id[$key] = $this->_getParam($key);
            }
            $this->_form->setId($id);
        } else {
            $this->_form->setId($this->_getParam($this->_form->getPrimaryKey()));
        }
    }

    public function jsonLoadAction()
    {
        $row = $this->_form->getRow();
        if (!$this->_hasPermissions($row, 'load')) {
            throw new Vps_Exception('You don\'t have the permission for this entry.');
        }

        if ($this->_form->getId()) {
            $this->view->data = $this->_form->load();
        }

        if ($this->getRequest()->getParam('meta')) {
            $this->_appendMetaData();
        }
    }

    protected function _appendMetaData()
    {
        $this->view->meta = array();
        $this->view->meta['form'] = $this->_form->getMetaData();
        $this->view->meta['buttons'] = $this->_buttons;
    }

    public function jsonSaveAction()
    {
        if(!isset($this->_permissions['save']) || !$this->_permissions['save']) {
            throw new Vps_Exception('Save is not allowed.');
        }
        $row = $this->_form->getRow();
        if (!$this->_hasPermissions($row, 'save')) {
            throw new Vps_Exception("Save is not allowed for this row.");
        }

        $data = $this->_form->prepareSave(null, $this->getRequest()->getParams());

        $this->_beforeSave($row);

        $primaryKey = $this->_form->getPrimaryKey();
        if (is_array($primaryKey)) $primaryKey = $primaryKey[1];
        if (!$row->$primaryKey) {
            if(!isset($this->_permissions['add']) || !$this->_permissions['add']) {
                throw new Vps_Exception('Add is not allowed.');
            }
            $this->_beforeInsert($row);
        }

        $data = $this->_form->save(null);

        $this->_afterSave($row);
        if (!$row->$primaryKey) {
            $this->afterInsert($row);
        }
        $this->view->data = $data;
    }

    public function jsonDeleteAction()
    {
        if(!isset($this->_permissions['delete']) || !$this->_permissions['delete']) {
            throw new Vps_Exception('Delete is not allowed.');
        }
        $row = $this->_form->getRow();
        if (!$this->_hasPermissions($row, 'delete')) {
            throw new Vps_Exception("Delete is not allowed for this row.");
        }
        $this->_form->delete(null);
    }

    protected function _beforeSave(Zend_Db_Table_Row_Abstract $row)
    {
    }

    protected function _afterSave(Zend_Db_Table_Row_Abstract $row)
    {
    }

    protected function _beforeInsert(Zend_Db_Table_Row_Abstract $row)
    {
    }

    protected function _afterInsert(Zend_Db_Table_Row_Abstract $row)
    {
    }

    protected function _hasPermissions($row, $action)
    {
        return true;
    }
}
