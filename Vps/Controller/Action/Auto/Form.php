<?php
abstract class Vps_Controller_Action_Auto_Form extends Vps_Controller_Action_Auto_Abstract
{
    protected $_fields = array();
    protected $_buttons = array('save' => true);

    public function init()
    {
        parent::init();
    }
    protected function _getFieldIndex($name)
    {
        foreach ($this->_fields as $k=>$c) {
            if (isset($c['name']) && $c['name'] == $name || isset($c['hiddenName']) && $c['hiddenName'] == $name) {
                return $k;
            }
        }
        foreach ($this->_fields as $k=>$c) {
            if (isset($c['id']) && $c['id'] == $name) {
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
        if (!isset($this->_table)) {
            throw new Vps_Exception("Either _table has to be set or _fetchData has to be overwritten.");
        }
        return $this->_table->find($id)->current();
    }

    protected function _hasPermissions($row, $action)
    {
        return true;
    }

    public function jsonLoadAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $row = $this->_fetchData($id);
            if (!$row) {
                throw new Vps_Exception("No database-entry with id '$id' found");
            }
            if (!$this->_hasPermissions((object)$row, 'load')) {
                throw new Vps_Exception("You don't have the permission to load id '$id'.");
            }
            if (is_array($row)) $row = (object)$row;
            $this->view->data = array();
            foreach ($this->_fields as $field) {
                if (isset($field['name'])) {
                    $name = $field['name'];
                } else if (isset($field['hiddenName'])) {
                    $name = $field['hiddenName'];
                } else {
                    $name = false;
                }
                if ($name) {
                    if (isset($field['findParent'])) {
                        $this->view->data[$name] = $this->_fetchFromParentRow($row, $field['findParent']);
                    } else {
                        $this->view->data[$name] = $this->_fetchFromRow($row, $name);
                    }
                }
            }
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

    public function jsonSaveAction()
    {
        if(!isset($this->_permissions['save']) || !$this->_permissions['save']) {
            throw new Vps_Exception("Save is not allowed.");
        }

        $primaryKey = $this->_primaryKey;
        $id = $this->getRequest()->getParam($primaryKey);
        if ($id) {
            $row = $this->_table->find($id)->current();
        } else {
            if(!isset($this->_permissions['add']) || !$this->_permissions['add']) {
                throw new Vps_Exception("Add is not allowed.");
            }
            $row = $this->_table->createRow();
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
        if (!$id) {
            $this->_beforeInsert($row);
        }
        if ($id && !$this->_hasPermissions($row, 'save')) {
            throw new Vps_Exception("You don't have the permission to save id '$id'.");
        }
        $row->save();
        $this->_afterSave($row);
        if (!$id) {
            $this->_afterInsert($row);
            $this->view->addedId = $row->$primaryKey;
        }
    }

    public function jsonDeleteAction()
    {
        if(!isset($this->_permissions['delete']) || !$this->_permissions['delete']) {
            throw new Vps_Exception("Delete is not allowed.");
        }
        $success = false;
        $id = $this->getRequest()->getParam($this->_primaryKey);

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
