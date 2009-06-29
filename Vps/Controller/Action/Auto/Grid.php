<?php
abstract class Vps_Controller_Action_Auto_Grid extends Vps_Controller_Action_Auto_Abstract
{
    protected $_columns = null;
    protected $_buttons = array('save', 'add', 'delete');
    protected $_editDialog = null;
    protected $_paging = 0;
    protected $_defaultOrder;
    protected $_filters = array();
    protected $_queryFields;
    protected $_querySeparator = ' ';
    protected $_sortable = true; //ob felder vom user sortiert werden können
    protected $_position;

    protected $_primaryKey;
    protected $_model;
    protected $_table;
    protected $_tableName;
    protected $_modelName;
    protected $_grouping = null;

    protected $_pdf = array();

    const PDF_ORIENTATION_PORTRAIT  = 'P';
    const PDF_ORIENTATION_LANDSCAPE = 'L';
    const PDF_FORMAT_A3 = 'A3';
    const PDF_FORMAT_A4 = 'A4';
    const PDF_EXPORTTYPE_TABLE = 1;
    const PDF_EXPORTTYPE_CONTAINER = 2;

    public function indexAction()
    {
        $this->view->controllerUrl = $this->getRequest()->getPathInfo();
        $this->view->xtype = 'vps.autogrid';
    }

    public function jsonIndexAction()
    {
       $this->indexAction();
    }

    protected function _initColumns()
    {
    }
    public function preDispatch()
    {
        parent::preDispatch();

        $addColumns = array();
        if (is_array($this->_columns)) $addColumns = $this->_columns;
        $this->_columns = new Vps_Collection();
        foreach ($addColumns as $k=>$column) {
            if (is_array($column)) {
                $columnObject = new Vps_Grid_Column();
                foreach ($column as $propName => $propValue) {
                    $columnObject->setProperty($propName, $propValue);
                }
                $this->_columns[] = $columnObject;
            } else {
                $this->_columns[] = $column;
            }
        }

        if (!isset($this->_model) && isset($this->_tableName)) {
            $this->setTable(new $this->_tableName());
        }
        if (!isset($this->_model) && isset($this->_table)) {
            $this->setTable($this->_table);
        }
        if (!isset($this->_model) && isset($this->_modelName)) {
            $this->_model = Vps_Model_Abstract::getInstance($this->_modelName);
        }

        $this->_initColumns();

        if (isset($this->_model)) {
            $this->_primaryKey = $this->_model->getPrimaryKey();
        }

        if (isset($this->_model) && $this->_position && in_array($this->_position, $this->_model->getColumns())) {
            $columnObject = new Vps_Grid_Column($this->_position);
            $columnObject->setHeader(' ')
                         ->setWidth(30)
                         ->setType('int');
            if (isset($this->_permissions['save']) && $this->_permissions['save']) {
                $columnObject->setEditor('PosField');
            }
            $this->_columns->prepend($columnObject);
            $this->_sortable = false;
            $this->_defaultOrder = $this->_position;
        }
        if (isset($this->_model) && ($info = $this->_getTableInfo())) {
            foreach ($this->_columns as $column) {
                if (!$column->getType() && isset($info['metadata'][$column->getDataIndex()])) {
                    $column->setType($this->_getTypeFromDbType($info['metadata'][$column->getDataIndex()]['DATA_TYPE']));
                }
            }
        }

        if ($this->_primaryKey) {
            $primaryFound = false;
            foreach ($this->_columns as $column) {
                if ($column->getDataIndex() == $this->_primaryKey) {
                    $primaryFound = true;
                }
            }
            if (!$primaryFound) {
                //primary key hinzufügen falls er noch nicht in gridColumns existiert
                $columnObject = new Vps_Grid_Column($this->_primaryKey);
                $info = $this->_getTableInfo();
                $columnObject->setType($this->_getTypeFromDbType($info['metadata'][$this->_primaryKey]['DATA_TYPE']));
                $this->_columns[] = $columnObject;
            }
        }

        //default durchsucht alle angezeigten felder
        if (!isset($this->_queryFields)) {
            $this->_queryFields = array();
            foreach ($this->_columns as $column) {
                $index = $column->getDataIndex();
                if ($info = $this->_getTableInfo()) {
                    if (!isset($info['metadata'][$index])) continue;
                } else {
                    if (!in_array($index, $this->_model->getColumns())) continue;
                }
                $this->_queryFields[] = $index;
            }
        }
        $info = $this->_getTableInfo();
        if (!in_array($this->_primaryKey, $this->_queryFields) && $info && !in_array($info['name'].'.'.$this->_primaryKey, $this->_queryFields)) {
            $this->_queryFields[] = $this->_primaryKey;
        }

        if (!isset($this->_defaultOrder)) {
            $this->_defaultOrder = $this->_columns->first()->getDataIndex();
        }

        if (is_string($this->_defaultOrder)) {

            $this->_defaultOrder = array(
                'field' => $this->_defaultOrder,
                'direction'   => 'ASC'
            );
        }

        if (method_exists($this, '_getWhereQuery')) {
            throw new Vps_Exception("_getWhereQuery doesn't exist anymore");
        }


        // Falls Filter einen Default-Wert hat:
        // - GET query-Parameter setzen,
        // - Im JavaScript nach rechts verschieben und Defaultwert setzen
        foreach ($this->_filters as $key => $filter) {
            $param = 'query_' . $key;
            if (isset($filter['default']) && !$this->_getParam($param)) {
                $this->_setParam($param, $filter['default']);
            }
        }
    }

    public function setTable($table)
    {
        $this->_model = new Vps_Model_Db(array(
            'table' => $table
        ));
    }
    public function setModel($model)
    {
        $this->_model = $model;
    }

    protected function _getModel()
    {
        return $this->_model;
    }

    protected function _getSelect()
    {
        $ret = $this->_model->select();

        $exprColumns = $this->_model->getExprColumns();
        foreach ($this->_columns as $column) {
            $d = $column->getData();
            if ($d instanceof Vps_Data_Table) {
                if (in_array($d->getField(), $exprColumns)) {
                    $ret->expr($d->getField());
                }
            }
        }

        $query = $this->getRequest()->getParam('query');
        $sk = isset($this->_filters['text']['skipWhere']) &&
            $this->_filters['text']['skipWhere'];


        if ($query && !$sk) {
            if (!isset($this->_queryFields)) {
                throw new Vps_Exception("queryFields which is required to use query-filters is not set.");
            }

            if ($this->_querySeparator) {
                $query = explode($this->_querySeparator, $query);
            } else {
                $query = array($query);
            }

            foreach ($query as $q) {
                if (strpos($q, ':') !== false) { // falls nach einem bestimmten feld gesucht wird zB id:15
                    $ret->where($this->_getQueryContainsColon($q));
                } else {
                    $ret->where($this->_getQueryExpression($q));
                }
            }
        }

        //check von QueryId
        $queryId = $this->getRequest()->getParam('queryId');
        if ($queryId) {
            $ret->where(new Vps_Model_Select_Expr_Equals($this->_primaryKey, $queryId));
        }

        //erzeugen von Filtern
        foreach ($this->_filters as $field=>$filter) {
            if ($field=='text') continue; //handled above
            if (isset($filter['skipWhere']) && $filter['skipWhere']) continue;
            if ($this->_getParam('query_'.$field)) {
                $ret->whereEquals($field, $this->_getParam('query_'.$field));
            }
            if ($filter['type'] == 'DateRange' && $this->_getParam($field.'_from')
                                               && $this->_getParam($field.'_to')) {
                $valueFrom = $this->_getParam($field.'_from');
                $valueTo = $this->_getParam($field.'_to');

                $ret->where(new Vps_Model_Select_Expr_Or(array(
                    new Vps_Model_Select_Expr_And(array(
                        new Vps_Model_Select_Expr_SmallerDate($field, $valueTo),
                        new Vps_Model_Select_Expr_HigherDate($field, $valueFrom)
                    )),
                    new Vps_Model_Select_Expr_Equals($field, $valueTo),
                    new Vps_Model_Select_Expr_Equals($field, $valueFrom)
                )));
            }
        }

        $where = $this->_getWhere();
        if (is_null($where)) return null;
        foreach ($where as $k=>$i) {
            if (is_int($k)) {
                $ret->where($i);
            } else {
                $ret->where($k, $i);
            }
        }
        return $ret;
    }

    private function _getQueryContainsColon ($query)
    {
        $availableColumns = $this->_model->getColumns();

        list($field, $value) = explode(':', $query);
        if (in_array($field, $availableColumns)) {
            if (is_numeric($value)) {
                return new Vps_Model_Select_Expr_Equals($field, $value);
            } else {
                return new Vps_Model_Select_Expr_Contains($field, $value);
            }
        } else {
            return null;
        }
    }
    protected function _getQueryExpression($query)
    {
        $containsExpression = array();
        foreach ($this->_queryFields as $queryField) {
            $containsExpression[] = new Vps_Model_Select_Expr_Contains($queryField, $query);
        }
        return new Vps_Model_Select_Expr_Or($containsExpression);
    }

    protected function _getWhere()
    {
        return array();
    }

    protected function _fetchData($order, $limit, $start)
    {
        if (!isset($this->_model)) {
            throw new Vps_Exception("Either _model has to be set or _fetchData has to be overwritten.");
        }

        $select = $this->_getSelect();
        if (is_null($select)) return null; //wenn _getSelect null zurückliefert nichts laden
        $select->limit($limit, $start);
        $select->order($this->_getOrder($order));
        return $this->_model->getRows($select);
    }

    protected function _getOrder($order)
    {
        // Falls Pos-Feld eingeschalten und das Order nicht andersweitig geändert wurde,
        // serverseitig sortiern, weil im Client ausgeschalten
        if ($this->_position && !$order) {
            return $this->_position;
        }
        return $order;
    }

    private function _getTableInfo()
    {
        $m = $this->_model;
        while ($m instanceof Vps_Model_Proxy) {
            $m = $m->getProxyModel();
        }

        if (!isset($this->_model) || !($m instanceof Vps_Model_Db)) {
            return null;
        }
        return $m->getTable()->info();
    }

    protected function _fetchCount()
    {
        if (!isset($this->_model)) {
            return count($this->_fetchData($this->_getOrder(null), 0, 0));
        }

        $select = $this->_getSelect();
        if (is_null($select)) return 0;
        return $this->_model->countRows($select);
    }


    public function jsonDataAction()
    {
        $limit = null; $start = null; $order = 0;
        if ($this->_paging) {
            $limit = $this->getRequest()->getParam('limit');
            $start = $this->getRequest()->getParam('start');
            if (!$limit) {
                if (!is_array($this->_paging) && $this->_paging > 0) {
                    $limit = $this->_paging;
                } else if (is_array($this->_paging) && isset($this->_paging['pageSize'])) {
                    $limit = $this->_paging['pageSize'];
                } else {
                    $limit = $this->_paging;
                }
            }

            $order = $this->_defaultOrder;
            if ($this->getRequest()->getParam('sort')) {
                $order['field'] = $this->getRequest()->getParam('sort');
            }
            if ($this->_getParam("direction") && $this->_getParam('direction') != 'undefined') {
                $order['direction'] = $this->_getParam('direction');
            }

// wird vermutlich nicht benötigt, da beim ersten laden 'sortInfo' in den metadaten drin ist
// falls es irgendwo benötigt wird wieder einkommentieren
//             $this->view->order = $order;
        }

        $primaryKey = $this->_primaryKey;

        $rowSet = $this->_fetchData($order, $limit, $start);
        if (!is_null($rowSet)) {
            $rows = array();
            foreach ($rowSet as $row) {
                $r = array();
                if (is_array($row)) {
                    $row = (object)$row;
                }
                if (!$this->_hasPermissions($row, 'load')) {
                    throw new Vps_Exception("You don't have the permissions to load this row");
                }
                foreach ($this->_columns as $column) {
                    if ($column->getShowIn() & Vps_Grid_Column::SHOW_IN_GRID) {
                        $data = $column->load($row, Vps_Grid_Column::ROLE_DISPLAY);
                        $r[$column->getDataIndex()] = $data;
                    }
                }
                if (!isset($r[$primaryKey]) && isset($row->$primaryKey)) {
                    $r[$primaryKey] = $row->$primaryKey;
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
        else if ($type == 'datetime') $type = 'date';
        else if ($type == 'date') $type = 'date';
        else if ($type == 'decimal') $type = 'float';
        else if (substr($type, 0, 6) == 'double') $type = 'float';
        else if ($type == 'time') $type = ''; //auto
        else $type = ''; //auto
        return $type;
    }

    protected function _appendMetaData()
    {
        $this->view->metaData = array();

        $this->view->metaData['helpText'] = $this->getHelpText();
        $this->view->metaData['root'] = 'rows';
        $this->view->metaData['id'] = $this->_primaryKey;
        if (isset($this->_paging['type']) && $this->_paging['type'] == 'Date') {
            //nix zu tun
        } else {
            $this->view->metaData['totalProperty'] = 'total';
        }
        $this->view->metaData['successProperty'] = 'success';
        if (!$this->_sortable || !$this->_getParam('sort')) {
            //sandard-sortierung
            $this->view->metaData['sortInfo'] = $this->_defaultOrder;
        } else {
            $this->view->metaData['sortInfo']['field'] = $this->_getParam('sort');
            $this->view->metaData['sortInfo']['direction'] = $this->_getParam('direction');
        }
        $this->view->metaData['columns'] = array();
        $this->view->metaData['fields'] = array();
        foreach ($this->_columns as $column) {
            if (!($column->getShowIn() & Vps_Grid_Column::SHOW_IN_GRID)) continue;
            $data = $column->getMetaData($this->_getModel(), $this->_getTableInfo());
            if ($data) {
                $this->view->metaData['columns'][] = $data;

                $d = array();
                if (isset($data['dataIndex'])) {
                    $d['name'] = $data['dataIndex'];
                }
                if (isset($data['type'])) {
                    $d['type'] = $data['type'];
                }

                if (isset($data['dateFormat'])) {
                    $d['dateFormat'] = $data['dateFormat'];
                }
                if (isset($data['dateFormat'])) {
                    $d['dateFormat'] = $data['dateFormat'];
                }
                if (isset($data['defaultValue'])) {
                    $d['defaultValue'] = $data['defaultValue'];
                }
                $this->view->metaData['fields'][] = $d;
            }

        }
        $this->view->metaData['buttons'] = (object)$this->_buttons;
        $this->view->metaData['permissions'] = (object)$this->_permissions;
        $this->view->metaData['paging'] = $this->_paging;
        $filters = array();
        foreach ($this->_filters as $k=>$f) {
            if (isset($f['field'])) $f['field'] = $f['field']->getMetaData($this->_getModel());
            $filters[$k] = $f;
        }
        $this->view->metaData['filters'] = (object)$filters;
        $this->view->metaData['sortable'] = $this->_sortable;
        $this->view->metaData['editDialog'] = $this->_editDialog;
        $this->view->metaData['grouping'] = $this->_grouping;
    }

    protected function _hasPermissions($row, $action)
    {
        return true;
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row, $submitRow)
    {
    }

    protected function _afterSave(Vps_Model_Row_Interface $row, $submitRow)
    {
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row, $submitRow)
    {
    }

    protected function _afterInsert(Vps_Model_Row_Interface $row, $submitRow)
    {
    }

    protected function _beforeDelete(Vps_Model_Row_Interface $row)
    {
    }

    protected function _afterDelete()
    {
    }

    public function jsonSaveAction()
    {
        if (!isset($this->_permissions['save']) || !$this->_permissions['save']) {
            throw new Vps_Exception("Save is not allowed.");
        }
        $success = false;

        $data = Zend_Json::decode($this->getRequest()->getParam("data"));
        $addedIds = array();
        ignore_user_abort(true);
        Zend_Registry::get('db')->beginTransaction();
        foreach ($data as $submitRow) {
            $id = $submitRow[$this->_primaryKey];
            if ($id) {
                $row = $this->_model->find($id)->current();
            } else {
                if (!isset($this->_permissions['add']) || !$this->_permissions['add']) {
                    throw new Vps_Exception("Add is not allowed.");
                }
                $row = $this->_model->createRow();
            }
            if (!$row) {
                throw new Vps_Exception("Can't find row with id '$id'.");
            }
            if (!$this->_hasPermissions($row, 'save')) {
                throw new Vps_Exception("You don't have the permissions to save this row.");
            }
            foreach ($this->_columns as $column) {
                if (!($column->getShowIn() & Vps_Grid_Column::SHOW_IN_GRID)) continue;
                //p($column->validate($row, $submitRow));
                $invalid = $column->validate($row, $submitRow);
                if ($invalid) {
                    throw new Vps_ClientException(implode("<br />", $invalid));
                }
                $column->prepareSave($row, $submitRow);
            }
            if (!$id) {
                $this->_beforeInsert($row, $submitRow);
            }
            $this->_beforeSave($row, $submitRow);


            $row->save();
            if (!$id) {
                $this->_afterInsert($row, $submitRow);
            }
            $this->_afterSave($row, $submitRow);
            if (!$id) {
                $addedIds[] = $row->id;
            }
        }
        Zend_Registry::get('db')->commit();
        $success = true;

        if ($addedIds) {
            $this->view->addedIds = $addedIds;
        }
        $this->view->success = $success;
    }

    public function jsonDeleteAction()
    {
        if (!isset($this->_permissions['delete']) || !$this->_permissions['delete']) {
            throw new Vps_Exception("Delete is not allowed.");
        }
        $ids = $this->getRequest()->getParam($this->_primaryKey);
        $ids = explode(';', $ids);

        ignore_user_abort(true);
        Zend_Registry::get('db')->beginTransaction();
        foreach ($ids as $id) {
            $row = $this->_model->find($id)->current();
            if (!$row) {
                throw new Vps_ClientException("Can't find row with id '$id'.");
            }
            if (!$this->_hasPermissions($row, 'delete')) {
                throw new Vps_Exception("You don't have the permissions to delete this row.");
            }
            $this->_beforeDelete($row);
            $row->delete();
            $this->_afterDelete();
        }
        Zend_Registry::get('db')->commit();
    }
    public function jsonDuplicateAction()
    {
        if (!isset($this->_permissions['duplicate']) || !$this->_permissions['duplicate']) {
            throw new Vps_Exception("Duplicate is not allowed.");
        }
        $ids = $this->getRequest()->getParam($this->_primaryKey);
        $ids = explode(';', $ids);

        $this->view->data = array('duplicatedIds' => array());
        ignore_user_abort(true);
        Zend_Registry::get('db')->beginTransaction();
        foreach ($ids as $id) {
            $row = $this->_model->find($id)->current();
            if (!$row) {
                throw new Vps_Exception("Can't find row with id '$id'.");
            }
            if (!$this->_hasPermissions($row, 'duplicate')) {
                throw new Vps_Exception("You don't have the permissions to duplicate this row.");
            }
            $new = $row->getRow()->duplicate();
            $this->view->data['duplicatedIds'][] = $new->{$this->_primaryKey};
        }
        Zend_Registry::get('db')->commit();
    }

    public function pdfAction()
    {
        if (!isset($this->_permissions['pdf']) || !$this->_permissions['pdf']) {
            throw new Vps_Exception("Pdf is not allowed.");
        }

        $pageMargin = 10;

        if (empty($this->_pdf['orientation'])) {
            $this->_pdf['orientation'] = self::PDF_ORIENTATION_PORTRAIT;
        }
        if (empty($this->_pdf['format'])) {
            $this->_pdf['format'] = self::PDF_FORMAT_A4;
        }
        if (!isset($this->_pdf['fields'])) {
            $this->_pdf['fields'] = array();
            foreach ($this->_columns as $column) {
                if (!($column->getShowIn() & Vps_Grid_Column::SHOW_IN_PDF)) continue;
                if ($column->getHeader()) {
                    $this->_pdf['fields'][] = $column->getName();
                }
            }
        }

        if (!is_array($this->_pdf['fields'])) {
            throw new Vps_Exception("PDF export fields must be of type `array`");
        }
        if (isset($this->_pdf['columns'])) {
            throw new Vps_Exception("PDF export fields key is labeld `fields`, not `columns`");
        }
        $tmpFields = array(); // Needed for correct sorting
        foreach ($this->_pdf['fields'] as $key => $mixed) {
            if (!is_array($mixed) && !is_string($mixed)) {
                throw new Vps_Exception("PDF export field `$mixed` must not be of type "
                                        .'`'.gettype($mixed).'`, only `string` or `array` allowed.');
            }
            if (is_string($mixed) && $this->_columns[$mixed]) {
                $tmpFields[$mixed] = array('header' => $this->_columns[$mixed]->getHeader(),
                                            'width'  => 0);
            } else if (is_array($mixed) && $this->_columns[$key]) {
                if (!isset($mixed['header'])) {
                    $this->_pdf['fields'][$key]['header'] =
                        $this->_columns[$key]->getHeader();
                }
                if (!isset($mixed['width'])) {
                    $this->_pdf['fields'][$key]['width'] = 0;
                }
                $tmpFields[$key] = $this->_pdf['fields'][$key];
            }
        }
        $this->_pdf['fields'] = $tmpFields;

        // Generate two times for correct page braking
        $breakBeforeRow = array();
        for ($i = 1; $i <= 2; $i++) {
            $pdf = new Vps_Grid_Pdf_Table($this->_pdf['orientation'], 'mm', $this->_pdf['format']);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetMargins($pageMargin, 20, $pageMargin);
            $pdf->SetFooterMargin(5);
            $pdf->SetAutoPageBreak(true, 20);
            $pdf->AliasNbPages();
            $pdf->AddPage();

            $pdf->setFields($this->_pdf['fields']);

    //         $pdf->SetBarcode(date("Y-m-d H:i:s", time()));

            $pdf->writeHeader();

            $rowSet = $this->_fetchData($this->_defaultOrder, null, null);

            if (!is_null($rowSet)) {
                $rowCount = 1;
                foreach ($rowSet as $row) {
                    if (is_array($row)) {
                        $row = (object)$row;
                    }

                    if ($i === 1) $pageNoBefore = $pdf->PageNo();

                    if ($i === 2 && in_array($rowCount, $breakBeforeRow)) {
                        $pdf->drawLines();
                        $pdf->AddPage();
                        $pdf->writeHeader();
                    }
                    $pdf->writeRow($row);

                    if ($i === 1 && $pageNoBefore != $pdf->PageNo()) {
                        $breakBeforeRow[] = $rowCount;
                        $pdf->AddPage();
                        $pdf->writeRow($row);
                    }
                    $rowCount++;
                }
            }
        }

        $pdf->drawLines();

        $pdf->output();
        $this->_helper->viewRenderer->setNoRender();
    }

    private function _createExportData($onlyShowIn)
    {
        $status = $this->_getExportStatus();

        $cache = $this->_getExportCache();
        $rowSet = $this->_fetchData($this->_defaultOrder, $this->_exportRowsPerRequest('collect'), $status['collected']);
        if (!$rowSet || !count($rowSet)) {
            $status['collected'] = $status['count'];
        } else {
            $exportData = $cache->load($this->_getParam('uniqueExportKey').'data');
            // Index 0 reserved for column headers
            if (!$exportData) $exportData = array(0 => array());

            $columns = $columnsHeader = array();
            foreach ($rowSet as $row) {
                if (is_array($row)) {
                    $row = (object)$row;
                }
                if (!$this->_hasPermissions($row, 'load')) {
                    throw new Vps_Exception("You don't have the permissions to load this row");
                }
                $columns = $columnsHeader = array();
                foreach ($this->_columns as $column) {
                    if (!($column->getShowIn() & $onlyShowIn)) continue;
                    $currentColumnHeader = $column->getHeader();

                    if (!is_null($currentColumnHeader)) {
                        $columnsHeader[] = (string)$currentColumnHeader;
                        $colVal = $column->load($row, Vps_Grid_Column::ROLE_EXPORT);
                        $setTypeTo = 'string';
                        if ($column->getType()) {
                            if ($column->getType() == 'boolean'
                             || $column->getType() == 'bool') $setTypeTo = 'bool';
                            if ($column->getType() == 'integer'
                             || $column->getType() == 'int') $setTypeTo = 'int';
                            if ($column->getType() == 'double'
                             || $column->getType() == 'float') $setTypeTo = 'float';
                            if ($column->getType() == 'null') $setTypeTo = 'null';
                        }
                        settype($colVal, $setTypeTo);
                        $columns[] = $colVal;
                    }
                }
                $exportData[] = $columns;
            }
            $exportData[0] = $columnsHeader;
            $cache->save($exportData, $this->_getParam('uniqueExportKey').'data');

            $status['collected'] = count($exportData) - 1;
        }
        $cache->save($status, $this->_getParam('uniqueExportKey').'status');
    }

    public function csvAction()
    {
        if (!isset($this->_permissions['csv']) || !$this->_permissions['csv']) {
            throw new Vps_Exception("CSV is not allowed.");
        }

        throw new Vps_Exception_NotYetImplemented('Excel Export has been converted'
            .' to view a progress bar and csv has not yet been converted.');

        $data = $this->_getExportData(Vps_Grid_Column::SHOW_IN_CSV);

        if (!is_null($data)) {
            $csvRows = array();
            foreach ($data as $row => $cols) {
                $cols = str_replace('"', '""', $cols);
                $csvRows[] = '"'. implode('";"', $cols) .'"';
            }

            $csvReturn = implode("\r\n", $csvRows);
        }

        $this->_helper->viewRenderer->setNoRender();
        $this->getResponse()->setHeader('Content-Type', "application/vnd-ms-excel")
                            ->setHeader('Content-Disposition', 'attachment; filename="csv_export_'.date('Y-m-d_Hi').'.csv"')
                            ->setHeader('ETag', '1253859135') //muss für IE 7 mitgegeben werden
                            ->setHeader('Last-Modified', gmdate("D, d M Y H:i:s \G\M\T", time() - 60*60*24))
                            ->setHeader('Accept-Ranges', 'none')
                            ->setHeader('Content-Length', strlen($csvReturn))
                            ->setHeader('Pragma', 0, true) //muss für IE 7 überschrieben werden
                            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true) //muss für IE 7 überschrieben werden
                            ->setBody($csvReturn);

                            $datei = "filename.xls";
    }

    private function _getColumnLetterByIndex($idx)
    {
        $letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M',
            'N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $maxLetterIndex = count($letters) - 1;
        if ($idx > $maxLetterIndex) {
            return $letters[floor(($idx) / count($letters))-1].$letters[($idx) % count($letters)];
        } else {
            return $letters[$idx];
        }
    }

    private function _getExportCache()
    {
        return Vps_Cache::factory('Core', 'File',
            array(
                'lifetime' => 900,
                'automatic_serialization' => true
            ),
            array(
                'cache_dir' => 'application/cache/model'
            )
        );
    }

    protected function _getExportStatus()
    {
        $cache = $this->_getExportCache();
        $status = $cache->load($this->_getParam('uniqueExportKey').'status');
        if (!$status) {
            $status = array(
                'count'     => $this->_fetchCount(),
                'collected' => 0,
                'done'      => 0,
                'downloadkey' => ''
            );
        }
        return $status;
    }

    protected function _exportRowsPerRequest($type = 'collect')
    {
        if ($type == 'done') {
            return mt_rand(80, 120);
        } else {
            return mt_rand(40, 60);
        }
    }

    private function _getXlsObject()
    {
        require_once 'PHPExcel.php';
        require_once 'PHPExcel/IOFactory.php';

        $cache = $this->_getExportCache();
        $xls = $cache->load($this->_getParam('uniqueExportKey').'xlsObject');
        if (!$xls) {
            $xls = new PHPExcel();
            $xls->getProperties()->setCreator("Vivid Planet Software GmbH");
            $xls->getProperties()->setLastModifiedBy("Vivid Planet Software GmbH");
            $xls->getProperties()->setTitle("VPS Excel Export");
            $xls->getProperties()->setSubject("VPS Excel Export");
            $xls->getProperties()->setDescription("VPS Excel Export");
            $xls->getProperties()->setKeywords("VPS Excel Export");
            $xls->getProperties()->setCategory("VPS Excel Export");

            $xls->setActiveSheetIndex(0);

            $colIndex = 0;
            $sheet = $xls->getActiveSheet();
            foreach ($this->_columns as $column) {
                if (!($column->getShowIn() & Vps_Grid_Column::SHOW_IN_XLS)) continue;
                if (is_null($column->getHeader())) continue;

                if ($column->getXlsWidth()) {
                    $width = $column->getXlsWidth();
                } else if ($column->getWidth()) {
                    $width = round($column->getWidth() / 6, 1);
                } else {
                    $width = 15;
                }

                $sheet->getColumnDimension($this->_getColumnLetterByIndex($colIndex))->setWidth($width);
                $colIndex++;
            }
            $cache->save($xls, $this->_getParam('uniqueExportKey').'xlsObject');
        }
        return $xls;
    }

    public function jsonXlsAction()
    {
        // e.g.: Stargate customer export: 128M memory_limit exhaust at 1500 lines
        ini_set('memory_limit', "768M");
        if (!isset($this->_permissions['xls']) || !$this->_permissions['xls']) {
            throw new Vps_Exception("XLS is not allowed.");
        }

        $status = $this->_getExportStatus();
        if ($status['collected'] < $status['count']) {
            $this->_createExportData(Vps_Grid_Column::SHOW_IN_XLS);
            $status = $this->_getExportStatus();
        } else if ($status['done'] < $status['count']) {
            $cache = $this->_getExportCache();
            $data = $cache->load($this->_getParam('uniqueExportKey').'data');
            if (isset($data) && !is_null($data) && $data) {
                $xls = $this->_getXlsObject();
                $sheet = $xls->getActiveSheet();
                foreach ($data as $row => $cols) {
                    // row ist index, das andre nicht, passt aber trotzdem so
                    // da ja in der ersten Zeile der Header steht
                    if ($status['done'] != 0 && $status['done'] >= $row) continue;
                    foreach ($cols as $col => $text) {
                        $cell = $this->_getColumnLetterByIndex($col).($row+1);
                        if (is_array($text)) $text = implode(', ', $text);
                        // make header bold
                        if ($row == 0) {
                            $sheet->getStyle($cell)->getFont()->setBold(true);
                        }
                        // TODO: Zeilenumbrüche
                        $textType = gettype($text);
                        $cellType = PHPExcel_Cell_DataType::TYPE_STRING;
                        if ($textType == 'boolean') $cellType = PHPExcel_Cell_DataType::TYPE_BOOL;
                        if ($textType == 'integer'
                         || $textType == 'double'
                         || $textType == 'float') $cellType = PHPExcel_Cell_DataType::TYPE_NUMERIC;
                        if ($textType == 'NULL') $cellType = PHPExcel_Cell_DataType::TYPE_NULL;

                        $sheet->setCellValueExplicit($cell, $text, $cellType);
                    }
                    if ($status['done'] + $this->_exportRowsPerRequest('done') <= $row) break;
                }
                $status['done'] = $row;
                $cache->save($status, $this->_getParam('uniqueExportKey').'status');
                $cache->save($xls, $this->_getParam('uniqueExportKey').'xlsObject');
            }
        } else {
            $cache = $this->_getExportCache();
            $status['downloadkey'] = uniqid();
            $cache->save($status, $this->_getParam('uniqueExportKey').'status');
            $objWriter = PHPExcel_IOFactory::createWriter($this->_getXlsObject(), 'Excel5');
            $objWriter->save('application/cache/model/'.$status['downloadkey'].'.xls');
        }

        $this->view->status = $status;
    }

    public function downloadExportFileAction()
    {
        if (!isset($this->_permissions['xls']) || !$this->_permissions['xls']) {
            throw new Vps_Exception("XLS is not allowed.");
        }
        if (!file_exists('application/cache/model/'.$this->_getParam('downloadkey').'.xls')) {
            throw new Vps_Exception('Wrong downloadkey submitted');
        }

        $file = array(
            'contents' => file_get_contents('application/cache/model/'.$this->_getParam('downloadkey').'.xls'),
            'mimeType' => 'application/msexcel',
            'downloadFilename' => 'export_'.date('Ymd-Hi').'.xls'
        );
        Vps_Media_Output::output($file);
        $this->_helper->viewRenderer->setNoRender();
    }

}
