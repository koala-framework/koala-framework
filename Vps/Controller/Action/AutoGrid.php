<?php
abstract class Vps_Controller_Action_AutoGrid extends Vps_Controller_Action
{
    protected $_gridColumns = array();
    protected $_gridButtons = array('save'=>true,
                                    'add'=>true,
                                    'delete'=>true);
    protected $_gridPermissions; //todo: Zend_Acl ??
    protected $_gridPaging = 0;
    protected $_gridTable = null;
    protected $_gridDefaultOrder = null;
    protected $_gridUseEditor;
    protected $_gridFilters = array();

    //deprecated:
    public function ajaxLoadAction() { $this->jsonLoadAction(); }
    public function ajaxSaveAction() { $this->jsonSaveAction(); }
    public function ajaxDeleteAction() { $this->jsonDeleteAction(); }

    public function init()
    {
        if (!isset($this->_gridUseEditor)) {
            foreach ($this->_gridColumns as $c) {
                if (isset($c['editor'])) {
                    $this->_gridUseEditor = true;
                    break;
                }
            }
        }

        $info = $this->_gridTable->info();
        foreach ($this->_gridColumns as $k=>$col) {
            if (!isset($col['type']) && isset($info['metadata'][$col['dataIndex']])) {
                $this->_gridColumns[$k]['type'] = $this->_getTypeFromDbType($info['metadata'][$col['dataIndex']]['DATA_TYPE']);
            } else {
                $this->_gridColumns[$k]['type'] = null;
            }
            if ($this->_gridColumns[$k]['type'] == 'date' && !isset($col['dateFormat'])) {
                $this->_gridColumns[$k]['dateFormat'] = 'Y-m-d';
            }
        }
        if (!isset($this->_gridPermissions)) {
            $this->_gridPermissions = $this->_gridButtons;
        }
    }

    protected function _fetchData($order, $limit, $start)
    {
        return $this->_gridTable->fetchAll(null, $order, $limit, $start);
    }

    protected function _fetchCount()
    {
        return $this->_gridTable->fetchCount();
    }

    public function jsonDataAction()
    {
        $limit = null; $start = null;
        if ($this->_gridPaging) {
            $limit = $this->getRequest()->getParam("limit");
            $start = $this->getRequest()->getParam('start');
            if(!$limit) $limit = $this->_gridPaging;
        }
        $order = $this->getRequest()->getParam("sort");
        if (!$order) $order = $this->_gridDefaultOrder;
        $order .= " ".$this->getRequest()->getParam("dir");
        $order = trim($order);

        $info = $this->_gridTable->info();
        $primaryKey = $info['primary'][1];

        $rowSet = $this->_fetchData($order, $limit, $start);
        if (!is_null($rowSet)) {
            $rows = array();
            foreach ($rowSet as $row) {
                $r = array();
                if ($row instanceof Zend_Db_Table_Row_Abstract) {
                    $row = $row->toArray();
                }
                foreach ($this->_gridColumns as $col) {
                    $r[$col['dataIndex']] = $row[$col['dataIndex']];
                }
                if (!isset($r[$primaryKey]) && isset($row[$primaryKey])) {
                    $r[$primaryKey] = $row[$primaryKey];
                }
                $rows[] = $r;
            }

            $this->view->rows = $rows;
            
            if ($this->_gridPaging) {
                $this->view->total = $this->_fetchCount();
            } else {
                $this->view->total = sizeof($rows);
            }
        } else {
            $this->view->total = 0;
            $this->view->rows = array();
        }

        if ($this->getRequest()->getParam('meta')) {
            $this->_appendMetaData();
        }
    }

    protected function _getTypeFromDbType($type)
    {
        if ($type == 'varchar') $type = 'string';
        else if (substr($type, 0, 7) == 'tinyint') $type = 'boolean';
        else if ($type == 'text') $type = 'string';
        else if ($type == 'tinytext') $type = 'string';
        return $type;
    }

    protected function _appendMetaData()
    {
        $info = $this->_gridTable->info();
        $metaData = array();
        $primaryFound = false;
        $primaryKey = $this->_getPrimaryKey();;

        foreach ($this->_gridColumns as $col) {
            $d = array();
            $d['name'] = $col['dataIndex'];
            if ($col['type']) $d['type'] = $col['type'];
            if (isset($col['dateFormat'])) {
                $d['dateFormat'] = $col['dateFormat'];
            }
            if (isset($col['defaultValue'])) {
                $d['defaultValue'] = $col['defaultValue'];
            }
            $metaData[] = $d;
            if ($col['dataIndex'] == $primaryKey) {
                $primaryFound = true;
            }
        }
        if (!$primaryFound) {
            //primary key hinzufügen falls er noch nicht in gridColumns existiert
            $d = array();
            $d['name'] = $primaryKey;
            $d['type'] = $this->_getTypeFromDbType($info['metadata'][$primaryKey]['DATA_TYPE']);
            $metaData[] = $d;
        }

        $sortInfo = array();
        $sortInfo['field'] = $this->_gridDefaultOrder;

        $this->view->metaData =
            array('fields'=>$metaData,
                    'root'=>'rows',
                    'id'=>$primaryKey,
                    'totalProperty'=>'total',
                    'successProperty'=>'success',
                    'sortInfo'=>$sortInfo,
                    'gridColumns'=>$this->_gridColumns,
                    'gridButtons'=>$this->_gridButtons,
                    'gridPaging'=>$this->_gridPaging,
                    'gridUseEditor'=>$this->_gridUseEditor,
                    'gridFilters'=>$this->_gridFilters);
    }

    protected function _getPrimaryKey()
    {
        $info = $this->_gridTable->info();
        return $info['primary'][1];
    }

    protected function _beforeSave(Zend_Db_Table_Row_Abstract $row)
    {
    }

    protected function _afterSave(Zend_Db_Table_Row_Abstract $row)
    {
    }
    
    public function jsonSaveAction()
    {
        if(!isset($this->_gridPermissions['save']) || !$this->_gridPermissions['save']) {
            throw new Vps_Exception("Save is not allowed.");
        }
        $success = false;

        $data = Zend_Json::decode(stripslashes($this->getRequest()->getParam("data")));
        $addedIds = array();
        foreach ($data as $submitRow) {
            $id = $submitRow[$this->_getPrimaryKey()];
            if ($id) {
                $row = $this->_gridTable->find($id)->current();
            } else {
                if(!isset($this->_gridPermissions['add']) || !$this->_gridPermissions['add']) {
                    throw new Vps_Exception("Add is not allowed.");
                }
                $row = $this->_gridTable->fetchNew();
            }
            if(!$row) {
                throw new Vps_Exception("Can't find row with id '$id'.");
            }
            foreach ($this->_gridColumns as $col) {
                if ((isset($col['allowSave']) && $col['allowSave'])
                    || (isset($col['editor']) && $col['editor']))
                {
                    if (isset($submitRow[$col['dataIndex']])) {
                        $row->$col['dataIndex'] = $submitRow[$col['dataIndex']];
                    }
                    if ($col['type'] == 'boolean') {
                        if(isset($submitRow[$col['dataIndex']])) {
                            $val = 1;
                        } else {
                            $val = 0;
                        }
                        $row->$col['dataIndex'] = $val;
                    }
                }
            }
            $this->_beforeSave($row);
            $row->save();
            $this->_afterSave($row);
            if (!$id) {
                $addedIds[] = $row->id;
            }
        }
        $success = true;

        if ($addedIds) {
            $this->view->addedIds = $addedIds;
        }
        $this->view->success = $success;
    }

    public function jsonDeleteAction()
    {
        if(!isset($this->_gridPermissions['delete']) || !$this->_gridPermissions['delete']) {
            throw new Vps_Exception("Delete is not allowed.");
        }
        $success = false;
        $id = $this->getRequest()->getParam($this->_getPrimaryKey());

        $row = $this->_gridTable->find($id)->current();
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
