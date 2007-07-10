<?php
abstract class Vps_Controller_Action_Auto_Form extends Vps_Controller_Action
{
    protected $_fields = array();
    protected $_buttons = array('save' => true);
    protected $_table;
    protected $_tableName;
    protected $_permissions; //todo: Zend_Acl ??

    //deprecated:
    public function ajaxLoadAction() { $this->jsonLoadAction(); }
    public function ajaxSaveAction() { $this->jsonSaveAction(); }
    public function ajaxDeleteAction() { $this->jsonDeleteAction(); }

    public function init()
    {
        if (!isset($this->_table)) {
            $this->_table = new $this->_tableName();
        }
        if (!isset($this->_permissions)) {
            $this->_permissions = $this->_buttons;
        }
    }
    protected function _getFieldIndex($name)
    {
        foreach ($this->_fields as $k=>$c) {
            if (isset($c['name']) && $c['name'] == $name || isset($c['hiddenName']) && $c['hiddenName'] == $name) {
                return $k;
            }
        }
        return false;
    }
    
    protected function _insertField($where, $field)
    {
        $where = $this->_getFieldIndex($where);
        if (!$where) {
            throw new Vps_Exception("Can't insert Field after '$where' which does not exist.");
        }
        array_splice($this->_fields, $where+1, 0, array($field));
    }

    protected function _fetchData($id)
    {
        return $this->_table->find($id)->current();
    }

    public function jsonLoadAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $row = $this->_fetchData($id);
            if (!$row) {
                throw new Vps_Exception("No database-entry with id '$id' found");
            }
            if (!is_array($row)) $row = $row->toArray();
            $this->view->data = array();
            foreach ($this->_fields as $field) {
                if(isset($field['name'])) {
                    $this->view->data[$field['name']] = $row[$field['name']];
                }
            }
            $this->view->data = $row;
        }

        if ($this->getRequest()->getParam('meta')) {
            $this->_appendMetaData();
        }
    }

    protected function _appendMetaData()
    {
        $this->view->meta = array();
        $this->view->meta['fields'] = $this->_fields;
        $this->view->meta['buttons'] = $this->_buttons;
    }

    protected function _getPrimaryKey()
    {
        $info = $this->_table->info();
        return $primaryKey = $info['primary'][1];
    }

    protected function _beforeSave(Zend_Db_Table_Row_Abstract $row)
    {
    }

    protected function _afterSave(Zend_Db_Table_Row_Abstract $row)
    {
    }
    
    public function jsonSaveAction()
    {
        if(!isset($this->_permissions['save']) || !$this->_permissions['save']) {
            throw new Vps_Exception("Save is not allowed.");
        }

        $primaryKey = $this->_getPrimaryKey();
        $id = $this->getRequest()->getParam($primaryKey);
        if ($id) {
            $row = $this->_table->find($id)->current();
        } else {
            if(!isset($this->_permissions['add']) || !$this->_permissions['add']) {
                throw new Vps_Exception("Add is not allowed.");
            }
            $row = $this->_table->fetchNew();
        }
        if(!$row) {
            throw new Vps_Exception("Can't find row with id '$id'.");
        }
        foreach ($this->_fields as $field) {
            $name = false;
            if (isset($field['name'])) $name = $field['name'];
            else if (isset($field['hiddenName'])) $name = $field['hiddenName'];
            if ($name && isset($row->$name)) {
                $row->$name = $this->getRequest()->getParam($name);
            }
        }
        $this->_beforeSave($row);
        $row->save();
        $this->_afterSave($row);
        if (!$id) {
            $this->view->addedId = $row->$primaryKey;
        }
    }

    public function jsonDeleteAction()
    {
        if(!isset($this->_permissions['delete']) || !$this->_permissions['delete']) {
            throw new Vps_Exception("Delete is not allowed.");
        }
        $success = false;
        $id = $this->getRequest()->getParam($this->_getPrimaryKey());

        $row = $this->_table->find($id)->current();
        if(!$row) {
            throw new Vps_Exception("Can't find row with id '$id'.");
        }
        try {
            $row->delete();
            $success = true;
        } catch (Vps_Exception $e) { //todo: nicht nur Vps_Exception fangen
            $this->view->error = $e->getMessage();
        }

        $this->view->success = $success;
    }
}
