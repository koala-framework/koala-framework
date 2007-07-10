<?php
abstract class Vps_Controller_Action_Auto_Grid extends Vps_Controller_Action
{
    protected $_gridColumns = array();
    protected $_gridButtons = array('save'=>true,
                                    'add'=>true,
                                    'delete'=>true);
    protected $_gridPermissions; //todo: Zend_Acl ??
    protected $_gridPaging = 0;
    protected $_gridTable;
    protected $_gridTableName;
    protected $_gridDefaultOrder;
    protected $_gridFilters = array();
    protected $_gridQueryFields;
    protected $_gridPrimaryKey;
    protected $_gridSortable = true;

    //deprecated:
    public function ajaxLoadAction() { $this->jsonLoadAction(); }
    public function ajaxSaveAction() { $this->jsonSaveAction(); }
    public function ajaxDeleteAction() { $this->jsonDeleteAction(); }

    public function init()
    {
        if (!isset($this->_gridTable) && isset($this->_gridTableName)) {
            $this->_gridTable = new $this->_gridTableName();
        }

        if (isset($this->_gridTable)) {
            $info = $this->_gridTable->info();
            if(!isset($this->_gridPrimaryKey)) {
                $info = $this->_gridTable->info();
                $this->_gridPrimaryKey = $info['primary'][1];
            }

            $primaryFound = false;
            foreach ($this->_gridColumns as $k=>$col) {
                if (!isset($col['type']) && isset($info['metadata'][$col['dataIndex']])) {
                    $this->_gridColumns[$k]['type'] = $this->_getTypeFromDbType($info['metadata'][$col['dataIndex']]['DATA_TYPE']);
                }
                if ($col['dataIndex'] == $this->_gridPrimaryKey) {
                    $primaryFound = true;
                }
            }
            if (!$primaryFound) {
                //primary key hinzufügen falls er noch nicht in gridColumns existiert
                $c = array();
                $c['dataIndex'] = $this->_gridPrimaryKey;
                $d['type'] = $this->_getTypeFromDbType($info['metadata'][$this->_gridPrimaryKey]['DATA_TYPE']);
                $this->_gridColumns[] = $c;
            }
        }

        foreach ($this->_gridColumns as $k=>$col) {
            if (!isset($col['type'])) {
                $this->_gridColumns[$k]['type'] = null;
            }
            if ($this->_gridColumns[$k]['type'] == 'date' && !isset($col['dateFormat'])) {
                $this->_gridColumns[$k]['dateFormat'] = 'Y-m-d';
            }
            if ($this->_gridColumns[$k]['type'] == 'date' && !isset($col['renderer'])) {
                $this->_gridColumns[$k]['renderer'] = 'Date';
            }
            if (isset($col['showDataIndex']) && $col['showDataIndex'] && !$this->_getColumnIndex($col['showDataIndex'])) {
                $this->_gridColumns[] = array('dataIndex' => $col['showDataIndex']);
            }
        }

        if (!isset($this->_gridPermissions)) {
            $this->_gridPermissions = $this->_gridButtons;
        }

        //default durchsucht alle angezeigten felder
        if (!isset($this->_gridQueryFields)) {
            $this->_gridQueryFields = array();
            foreach ($this->_gridColumns as $k=>$col) {
                $this->_gridQueryFields[] = $col['dataIndex'];
            }
        }

        if ($this->_gridSortable && !isset($this->_gridDefaultOrder)) {
            $this->_gridDefaultOrder = $this->_gridColumns[0]['dataIndex'];
        }
    }

    protected function _getColumnIndex($name)
    {
        foreach ($this->_gridColumns as $k=>$c) {
            if (isset($c['dataIndex']) && $c['dataIndex'] == $name) {
                return $k;
            }
        }
        return false;
    }
    
    protected function _insertColumn($where, $column)
    {
        $where = $this->_getColumnIndex($where);
        if (!$where) {
            throw new Vps_Exception("Can't insert Column after '$where' which does not exist.");
        }
        array_splice($this->_gridColumns, $where+1, 0, array($column));
    }

    protected function _getWhere()
    {
        $where = array();
        $query = $this->getRequest()->getParam('query');
        if ($query) {
            if (!isset($this->_gridQueryFields)) {
                throw new Vps_Exception("gridQueryFields which is required to use query-filters is not set.");
            }
            $whereQuery = array();
            $db = $this->_gridTable->getAdapter();
            $query = explode(' ', $query);
            foreach($query as $q) {
                foreach($this->_gridQueryFields as $f) {
                    $whereQuery[] = $db->quoteInto("$f LIKE ?", "%$q%");
                }
            }
            $where[] = implode(' OR ', $whereQuery);
        }
        $queryId = $this->getRequest()->getParam('queryId');
        if ($queryId) {
            $where[$this->_gridPrimaryKey.' = ?'] = $queryId;
        }
        return $where;
    }

    protected function _fetchData($order, $limit, $start)
    {
        if (!isset($this->_gridTable)) {
            throw new Vps_Exception("Either _gridTable has to be set or _fetchData has to be overwritten.");
        }
        return $this->_gridTable->fetchAll($this->_getWhere(), $order, $limit, $start);
    }

    protected function _fetchCount()
    {
        if (!isset($this->_gridTable)) {
            throw new Vps_Exception("Either _gridTable has to be set or _fetchData has to be overwritten.");
        }
        $select = $this->_gridTable->getAdapter()->select();
        $info = $this->_gridTable->info();

        $select->from($info['name'], 'COUNT(*)', $info['schema']);

        $where = (array) $this->_getWhere();
        foreach ($where as $key => $val) {
            // is $key an int?
            if (is_int($key)) {
                // $val is the full condition
                $select->where($val);
            } else {
                // $key is the condition with placeholder,
                // and $val is quoted into the condition
                $select->where($key, $val);
            }
        }

        // return the results
        $stmt = $this->_gridTable->getAdapter()->query($select);
        return $stmt->fetchColumn();
    }

    public function jsonDataAction()
    {
        $limit = null; $start = null;
        if ($this->_gridPaging) {
            $limit = $this->getRequest()->getParam("limit");
            $start = $this->getRequest()->getParam('start');
            if(!$limit) {
                if(!is_array($this->_gridPaging) && $this->_gridPaging > 0) {
                    $limit = $this->_gridPaging;
                } else if (is_array($this->_gridPaging) && isset($this->_gridPaging['pageSize'])) {
                    $limit = $this->_gridPaging['pageSize'];
                } else {
                    $limit = $this->_gridPaging;
                }
            }
        }
        $order = $this->getRequest()->getParam("sort");
        if (!$order) $order = $this->_gridDefaultOrder;
        if($this->getRequest()->getParam("dir")!='UNDEFINED') {
            $order .= " ".$this->getRequest()->getParam("dir");
        }
        $order = trim($order);

        $primaryKey = $this->_gridPrimaryKey;

        $rowSet = $this->_fetchData($order, $limit, $start);
        if (!is_null($rowSet)) {
            $rows = array();
            foreach ($rowSet as $row) {
                $r = array();
                if ($row instanceof Zend_Db_Table_Row_Abstract) {
                    $row = $row->toArray();
                }
                foreach ($this->_gridColumns as $col) {
                    if(!is_null($row[$col['dataIndex']]) && !isset($row[$col['dataIndex']])) {
                        throw new Vps_Exception("Index '$col[dataIndex]' not found in row.");
                    }
                    $r[$col['dataIndex']] = $row[$col['dataIndex']];
                }
                if (!isset($r[$primaryKey]) && isset($row[$primaryKey])) {
                    $r[$primaryKey] = $row[$primaryKey];
                }
                $rows[] = $r;
            }

            $this->view->rows = $rows;
            if (isset($this->_gridPaging['type']) && $this->_gridPaging['type'] == 'Date') {
                //nix zu tun
            } else if ($this->_gridPaging) {
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
        else if (substr($type, -3) == 'int') $type = 'int';
        return $type;
    }

    protected function _appendMetaData()
    {
        $fields = array();
        foreach ($this->_gridColumns as $col) {
            $d = array();
            $d['name'] = $col['dataIndex'];
            if (isset($col['type']) && $col['type']) {
                $d['type'] = $col['type'];
            }
            if (isset($col['dateFormat'])) {
                $d['dateFormat'] = $col['dateFormat'];
            }
            if (isset($col['defaultValue'])) {
                $d['defaultValue'] = $col['defaultValue'];
            }
            $fields[] = $d;
        }

        $this->view->metaData = array();
        $this->view->metaData['fields'] = $fields;
        $this->view->metaData['root'] = 'rows';
        $this->view->metaData['id'] = $this->_gridPrimaryKey;
        if (isset($this->_gridPaging['type']) && $this->_gridPaging['type'] == 'Date') {
            //nix zu tun
        } else {
            $this->view->metaData['totalProperty'] = 'total';
        }
        $this->view->metaData['successProperty'] = 'success';
        if ($this->_gridSortable && !$this->getRequest()->getParam('sort')) {
            //sandard-sortierung
            $this->view->metaData['sortInfo']['field'] = $this->_gridDefaultOrder;
            $this->view->metaData['sortInfo']['dir'] = 'ASC';
        } else if ($this->_gridSortable) {
            $this->view->metaData['sortInfo']['field'] = $this->getRequest()->getParam('sort');
            $this->view->metaData['sortInfo']['dir'] = $this->getRequest()->getParam('dir');
        }
        $this->view->metaData['gridColumns'] = $this->_gridColumns;
        $this->view->metaData['gridButtons'] = $this->_gridButtons;
        $this->view->metaData['gridPaging'] = $this->_gridPaging;
        $this->view->metaData['gridFilters'] = $this->_gridFilters;
        $this->view->metaData['gridSortable'] = $this->_gridSortable;
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
            $id = $submitRow[$this->_gridPrimaryKey];
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
        $id = $this->getRequest()->getParam($this->_gridPrimaryKey);

        $row = $this->_gridTable->find($id)->current();
        if(!$row) {
            throw new Vps_Exception("Can't find row with id '$id'.");
        }
        try {
            $row->delete();
            $success = true;
        } catch (Vps_ClientException $e) { //todo: nicht nur Vps_Exception fangen
            $this->view->error = $e->getMessage();
        }

        $this->view->success = $success;
    }
}
