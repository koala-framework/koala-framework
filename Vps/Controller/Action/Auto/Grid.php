<?php
abstract class Vps_Controller_Action_Auto_Grid extends Vps_Controller_Action
{
    protected $_columns = array();
    protected $_buttons = array('save'=>true,
                                    'add'=>true,
                                    'delete'=>true);
    protected $_permissions; //todo: Zend_Acl ??
    protected $_paging = 0;
    protected $_table;
    protected $_tableName;
    protected $_defaultOrder;
    protected $_filters = array();
    protected $_queryFields;
    protected $_primaryKey;
    protected $_sortable = true;

    //deprecated:
    public function ajaxLoadAction() { $this->jsonLoadAction(); }
    public function ajaxSaveAction() { $this->jsonSaveAction(); }
    public function ajaxDeleteAction() { $this->jsonDeleteAction(); }

    public function init()
    {
        if (!isset($this->_table) && isset($this->_tableName)) {
            $this->_table = new $this->_tableName();
        }

        if (isset($this->_table)) {
            $info = $this->_table->info();
            if(!isset($this->_primaryKey)) {
                $info = $this->_table->info();
                $this->_primaryKey = $info['primary'][1];
            }

            $primaryFound = false;
            foreach ($this->_columns as $k=>$col) {
                if (!isset($col['type']) && isset($info['metadata'][$col['dataIndex']])) {
                    $this->_columns[$k]['type'] = $this->_getTypeFromDbType($info['metadata'][$col['dataIndex']]['DATA_TYPE']);
                }
                if ($col['dataIndex'] == $this->_primaryKey) {
                    $primaryFound = true;
                }
            }
            if (!$primaryFound) {
                //primary key hinzufügen falls er noch nicht in gridColumns existiert
                $c = array();
                $c['dataIndex'] = $this->_primaryKey;
                $d['type'] = $this->_getTypeFromDbType($info['metadata'][$this->_primaryKey]['DATA_TYPE']);
                $this->_columns[] = $c;
            }
        }

        foreach ($this->_columns as $k=>$col) {
            if (!isset($col['type'])) {
                $this->_columns[$k]['type'] = null;
            }
            if ($this->_columns[$k]['type'] == 'date' && !isset($col['dateFormat'])) {
                $this->_columns[$k]['dateFormat'] = 'Y-m-d';
            }
            if ($this->_columns[$k]['type'] == 'date' && !isset($col['renderer'])) {
                $this->_columns[$k]['renderer'] = 'Date';
            }
            if (isset($col['showDataIndex']) && $col['showDataIndex'] && !$this->_getColumnIndex($col['showDataIndex'])) {
                $this->_columns[] = array('dataIndex' => $col['showDataIndex']);
            }
        }

        if (!isset($this->_permissions)) {
            $this->_permissions = $this->_buttons;
        }

        //default durchsucht alle angezeigten felder
        if (!isset($this->_queryFields)) {
            $this->_queryFields = array();
            foreach ($this->_columns as $k=>$col) {
                $this->_queryFields[] = $col['dataIndex'];
            }
        }

        if ($this->_sortable && !isset($this->_defaultOrder)) {
            $this->_defaultOrder = $this->_columns[0]['dataIndex'];
        }
    }

    protected function _getColumnIndex($name)
    {
        foreach ($this->_columns as $k=>$c) {
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
        array_splice($this->_columns, $where+1, 0, array($column));
    }

    protected function _getWhere()
    {
        $where = array();
        $query = $this->getRequest()->getParam('query');
        if ($query) {
            if (!isset($this->_queryFields)) {
                throw new Vps_Exception("queryFields which is required to use query-filters is not set.");
            }
            $whereQuery = array();
            $db = $this->_table->getAdapter();
            $query = explode(' ', $query);
            foreach($query as $q) {
                foreach($this->_queryFields as $f) {
                    $whereQuery[] = $db->quoteInto("$f LIKE ?", "%$q%");
                }
            }
            $where[] = implode(' OR ', $whereQuery);
        }
        $queryId = $this->getRequest()->getParam('queryId');
        if ($queryId) {
            $where[$this->_primaryKey.' = ?'] = $queryId;
        }
        return $where;
    }

    protected function _fetchData($order, $limit, $start)
    {
        if (!isset($this->_table)) {
            throw new Vps_Exception("Either _table has to be set or _fetchData has to be overwritten.");
        }
        return $this->_table->fetchAll($this->_getWhere(), $order, $limit, $start);
    }

    protected function _fetchCount()
    {
        if (!isset($this->_table)) {
            throw new Vps_Exception("Either _gridTable has to be set or _fetchData has to be overwritten.");
        }
        $select = $this->_table->getAdapter()->select();
        $info = $this->_table->info();

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
        $stmt = $this->_table->getAdapter()->query($select);
        return $stmt->fetchColumn();
    }

    public function jsonDataAction()
    {
        $limit = null; $start = null;
        if ($this->_paging) {
            $limit = $this->getRequest()->getParam("limit");
            $start = $this->getRequest()->getParam('start');
            if(!$limit) {
                if(!is_array($this->_paging) && $this->_paging > 0) {
                    $limit = $this->_paging;
                } else if (is_array($this->_paging) && isset($this->_paging['pageSize'])) {
                    $limit = $this->_paging['pageSize'];
                } else {
                    $limit = $this->_paging;
                }
            }
        }
        $order = $this->getRequest()->getParam("sort");
        if (!$order) $order = $this->_defaultOrder;
        if($this->getRequest()->getParam("dir")!='UNDEFINED') {
            $order .= " ".$this->getRequest()->getParam("dir");
        }
        $order = trim($order);

        $primaryKey = $this->_primaryKey;

        $rowSet = $this->_fetchData($order, $limit, $start);
        if (!is_null($rowSet)) {
            $rows = array();
            foreach ($rowSet as $row) {
                $r = array();
                if ($row instanceof Zend_Db_Table_Row_Abstract) {
                    $row = $row->toArray();
                }
                foreach ($this->_columns as $col) {
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
            if (isset($this->_paging['type']) && $this->_paging['type'] == 'Date') {
                //nix zu tun
            } else if ($this->_paging) {
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
        foreach ($this->_columns as $col) {
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
        $this->view->metaData['id'] = $this->_primaryKey;
        if (isset($this->_paging['type']) && $this->_paging['type'] == 'Date') {
            //nix zu tun
        } else {
            $this->view->metaData['totalProperty'] = 'total';
        }
        $this->view->metaData['successProperty'] = 'success';
        if ($this->_sortable && !$this->getRequest()->getParam('sort')) {
            //sandard-sortierung
            $this->view->metaData['sortInfo']['field'] = $this->_defaultOrder;
            $this->view->metaData['sortInfo']['dir'] = 'ASC';
        } else if ($this->_sortable) {
            $this->view->metaData['sortInfo']['field'] = $this->getRequest()->getParam('sort');
            $this->view->metaData['sortInfo']['dir'] = $this->getRequest()->getParam('dir');
        }
        $this->view->metaData['columns'] = $this->_columns;
        $this->view->metaData['buttons'] = $this->_buttons;
        $this->view->metaData['paging'] = $this->_paging;
        $this->view->metaData['filters'] = $this->_filters;
        $this->view->metaData['sortable'] = $this->_sortable;
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
        $success = false;

        $data = Zend_Json::decode(stripslashes($this->getRequest()->getParam("data")));
        $addedIds = array();
        foreach ($data as $submitRow) {
            $id = $submitRow[$this->_primaryKey];
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
            foreach ($this->_columns as $col) {
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
            if (!$id) {
                $this->_beforeInsert($row);
            }
            $this->_beforeSave($row);
            $row->save();
            if (!$id) {
                $this->_afterInsert($row);
            }
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
        } catch (Vps_ClientException $e) { //todo: nicht nur Vps_Exception fangen
            $this->view->error = $e->getMessage();
        }

        $this->view->success = $success;
    }
}
