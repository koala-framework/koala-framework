<?php
abstract class Vps_Controller_Action_Auto_Form extends Vps_Controller_Action_Auto_Abstract
{
    protected $_form;
    protected $_fields = array(); //deprecated
    protected $_buttons = array();

    public function indexAction()
    {
       $this->view->ext('Vps.Auto.FormPanel', $this->_form->getProperties());
    }

    protected function _initFields()
    {
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if (!isset($this->_form)) {
            $this->_form = new Vps_Auto_Form();
        }

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

        if (!$this->_form->getId()) {
            if (is_array($this->_form->getPrimaryKey())) {
                foreach ($this->_form->getPrimaryKey() as $key) {
                    $id[$key] = $this->_getParam($key);
                }
                $this->_form->setId($id);
            } else {
                $this->_form->setId($this->_getParam($this->_form->getPrimaryKey()));
            }
        }
    }

    public function jsonLoadAction()
    {
        if ($this->_form->getId()) { //nur laden wennn einen id über get daherkommt
            $row = $this->_form->getRow();

            if (!$this->_hasPermissions($row, 'load')) {
                throw new Vps_Exception('You don\'t have the permission for this entry.');
            }
            $this->view->data = $this->_form->load(null);
        }

        if ($this->getRequest()->getParam('meta')) {
            $this->_appendMetaData();
        }
    }

    protected function _appendMetaData()
    {
        $this->view->meta = array();
        $this->view->meta['form'] = $this->_form->getMetaData();
        $this->view->meta['buttons'] = (object)$this->_buttons; //in objekt casten damit json kein [] sondern {} ausgibt
        $this->view->meta['permissions'] = (object)$this->_permissions; //in objekt casten damit json kein [] sondern {} ausgibt
    }

    public function jsonSaveAction()
    {
        $row = $this->_form->getRow();

        $data = $this->_form->prepareSave(null, $this->getRequest()->getParams());

        $insert = false;

        if ($row) {
            $primaryKey = $this->_form->getPrimaryKey();
            if (is_array($primaryKey)) $primaryKey = $primaryKey[1];
            if (!$row->$primaryKey) {
                $insert = true;
            }
            if ($insert) {
                if (!isset($this->_permissions['add']) || !$this->_permissions['add']) {
                    throw new Vps_Exception('Add is not allowed.');
                }
                $this->_beforeInsert($row);
            } else {
                if (!isset($this->_permissions['save']) || !$this->_permissions['save']) {
                    throw new Vps_Exception('Save is not allowed.');
                }
            }
            $this->_beforeSave($row);
        }

        //erst hier unten Berechtigungen überprüfen, damit beforeInsert usw vorher noch ausgeführt
        //wird und eventuelle Daten gesetzt werden
        if (!$this->_hasPermissions($row, 'save')) {
            throw new Vps_Exception("Save is not allowed for this row.");
        }

        $data = $this->_form->save(null, $this->getRequest()->getParams());

        if ($row) {
            if ($insert) {
                $this->_afterInsert($row);
            }
            $this->_afterSave($row);
        }

        $this->view->data = $data;
    }

    public function jsonDeleteAction()
    {
        if (!isset($this->_permissions['delete']) || !$this->_permissions['delete']) {
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
