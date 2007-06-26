<?php
abstract class Vps_Controller_Action_AutoForm extends Vps_Controller_Action
{
    protected $_formFields = array();
    protected $_formButtons = array('save' => true);
    protected $_formTable;
    protected $_formPermissions; //todo: Zend_Acl ??

    //deprecated:
    public function ajaxLoadAction() { $this->jsonLoadAction(); }
    public function ajaxSaveAction() { $this->jsonSaveAction(); }
    public function ajaxDeleteAction() { $this->jsonDeleteAction(); }

    public function init()
    {
        if (!isset($this->_formPermissions)) {
            $this->_formPermissions = $this->_formButtons;
        }
    }

    protected function _fetchData($id)
    {
        return $this->_formTable->find($id)->current();
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
            foreach ($this->_formFields as $field) {
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
        $this->view->meta['formFields'] = $this->_formFields;
        $this->view->meta['formButtons'] = $this->_formButtons;
    }

    protected function _getPrimaryKey()
    {
        $info = $this->_formTable->info();
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
        if(!isset($this->_formPermissions['save']) || !$this->_formPermissions['save']) {
            throw new Vps_Exception("Save is not allowed.");
        }

        $primaryKey = $this->_getPrimaryKey();
        $id = $this->getRequest()->getParam($primaryKey);
        if ($id) {
            $row = $this->_formTable->find($id)->current();
        } else {
            if(!isset($this->_formPermissions['add']) || !$this->_formPermissions['add']) {
                throw new Vps_Exception("Add is not allowed.");
            }
            $row = $this->_formTable->fetchNew();
        }
        if(!$row) {
            throw new Vps_Exception("Can't find row with id '$id'.");
        }
        foreach ($this->_formFields as $field) {
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
        if(!isset($this->_formPermissions['delete']) || !$this->_formPermissions['delete']) {
            throw new Vps_Exception("Delete is not allowed.");
        }
        $success = false;
        $id = $this->getRequest()->getParam($this->_getPrimaryKey());

        $row = $this->_formTable->find($id)->current();
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
