<?php
abstract class Vps_Controller_Action_Auto_Form extends Vps_Controller_Action_Auto_Abstract
{
    protected $_fields = array();
    protected $_buttons = array('save' => true);

    public function indexAction()
    {
       $this->view->ext('Vps.Auto.Form');
    }

    public function jsonIndexAction()
    {
       $this->indexAction();
    }

    public function init()
    {
        parent::init();
    }

    protected function _getId()
    {
        return array($this->getRequest()->getParam('id'));
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

    protected function _fetchData()
    {
        if (!isset($this->_table)) {
            throw new Vps_Exception('Either _table has to be set or _fetchData has to be overwritten.');
        }
        $rowset = null;
        if (is_array($this->_primaryKey)) {
            $where = array();
            foreach ($this->_primaryKey as $key) {
                $id = $this->_getParam($key);
                if ($id) {
                    $where[$key . ' = ?'] = $id;
                }
            }
            if (!empty($where)) {
                $rowset = $this->_table->fetchAll($where);
            }
        } else {
            $id = $this->_getParam($this->_primaryKey);
            if ($id) {
                $rowset = $this->_table->find($id);
            }
        }
        if (!$rowset) {
            return null;
        } else {
            if ($rowset->count() == 0) {
                throw new Vps_ClientException('No database-entry found.');
            } else if ($rowset->count() > 1) {
                throw new Vps_ClientException('More than one database-entry found.');
            } else {
                return $rowset->current();
            }
        }
    }

    protected function _hasPermissions($row, $action)
    {
        return true;
    }

    public function jsonLoadAction()
    {
        $row = $this->_fetchData();
        if ($row) {
            $row = (object)$row;
            if (!$this->_hasPermissions($row, 'load')) {
                throw new Vps_Exception('You don\'t have the permission to this entry.');
            }
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
            throw new Vps_Exception('Save is not allowed.');
        }

        $row = (object)$this->_fetchData();
        $add = false;
        if (!$row) {
            if(!isset($this->_permissions['add']) || !$this->_permissions['add']) {
                throw new Vps_Exception('Add is not allowed.');
            }
            $add = true;
            $row = $this->_table->createRow();
        }
        if(!$row) {
            throw new Vps_Exception('Can\'t find row.');
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
        if ($add) {
            $this->_beforeInsert($row);
        }
        if (!$add && !$this->_hasPermissions($row, 'save')) {
            throw new Vps_Exception('You don\'t have the permission to save current row.');
        }
        $row->save();
        $this->_afterSave($row);
        if ($add) {
            $this->_afterInsert($row);
            $primaryKey = $this->_primaryKey;
            if (is_array($primaryKey)) {
                $addedId = array();
                foreach ($primaryKey as $key) {
                    $addedId[$key] = $row->$key;
                }
            } else {
                $addedId = $row->$primaryKey;
            }
            $this->view->addedId = $addedId;
        }
    }

    public function jsonDeleteAction()
    {
        if(!isset($this->_permissions['delete']) || !$this->_permissions['delete']) {
            throw new Vps_Exception('Delete is not allowed.');
        }
        $success = false;

        $row = $this->_fetchData();
        if(!$row) {
            throw new Vps_Exception('Can\'t find row to delete.');
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
