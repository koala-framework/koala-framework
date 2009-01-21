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
        $config = array(
            'controllerUrl' => $this->getRequest()->getPathInfo()
        );
        $this->view->ext('Vps.Auto.GridPanel', $config);
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

        // Falls Filter einen Default-Wert hat:
        // - GET query-Parameter setzen,
        // - Im JavaScript nach rechts verschieben und Defaultwert setzen
        foreach ($this->_filters as $key => $filter) {
            $param = 'query_' . $key;
            if (isset($filter['default']) && !$this->_getParam($param)) {
                $this->_setParam($param, $filter['default']);
            }
        }

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
            $this->_model = new $this->_modelName();
        }

        $this->_initColumns();

        if (isset($this->_model)) {
            $this->_primaryKey = $this->_model->getPrimaryKey();
        }

        if (isset($this->_model) && ($info = $this->_getTableInfo())) {
            if ($this->_position && array_search($this->_position, $info['cols'])) {
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

    protected function _getSelect()
    {
        $ret = $this->_model->select();

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
                $ret->where(new Vps_Model_Select_Expr_Equals($field, $this->_getParam('query_'.$field)));
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
        if (!isset($this->_model) || !($this->_model instanceof Vps_Model_Db)) {
            return null;
        }
        return $this->_model->getTable()->info();
    }

    protected function _fetchCount()
    {
        if (!isset($this->_model)) {
            throw new Vps_Exception("Either _model has to be set or _fetchData has to be overwritten.");
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
            $data = $column->getMetaData($this->_getTableInfo());
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
        $this->view->metaData['filters'] = (object)$this->_filters;
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

    private function _getExportData($onlyShowIn)
    {
        $rowSet = $this->_fetchData($this->_defaultOrder, null, null);

        if (!$rowSet) {
            return null;
        } else {
            // $exportData = array( row => array( col => 'data' ) )
            // Index 0 reserved for column headers
            $exportData = array(0 => array());
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
                        $columnsHeader[] = $currentColumnHeader;
                        $columns[] = $column->load($row, Vps_Grid_Column::ROLE_EXPORT);
                    }
                }

                $exportData[] = $columns;
            }

            $exportData[0] = $columnsHeader;
        }

        return $exportData;
    }

    public function csvAction()
    {
        if (!isset($this->_permissions['csv']) || !$this->_permissions['csv']) {
            throw new Vps_Exception("CSV is not allowed.");
        }

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

    public function xlsAction()
    {
        if (!isset($this->_permissions['xls']) || !$this->_permissions['xls']) {
            throw new Vps_Exception("XLS is not allowed.");
        }
        require_once 'Spreadsheet/Excel/Writer.php';

        $xls = new Spreadsheet_Excel_Writer();
        $xls->send('xls_export_'.date('Y-m-d_Hi').'.xls');
        header('ETag:523566889'); //muss für IE 7 überschrieben werden

        $sheet = $xls->addWorksheet('export_'. date('Y-m-d_H-i'));
        $sheet->setInputEncoding('UTF-8');

        $colHeaderOptions = array();
        $colOptions = array();
        $i = 0;

        foreach ($this->_columns as $column) {
            if (!($column->getShowIn() & Vps_Grid_Column::SHOW_IN_XLS)) continue;
            if (is_null($column->getHeader())) continue;

            $options = $column->getXlsOptions();
            if ($options) {
                if (is_array($options)) {
                    $options = new Vps_Grid_Xls_Options($options);
                }
            } else {
                $options = new Vps_Grid_Xls_Options();
            }

            if ($options->getWidth() === null) {
                if ($column->getXlsWidth()) {
                    $options->setWidth($column->getXlsWidth());
                } else {
                    if ($column->getWidth()) {
                        $options->setWidth(round($column->getWidth() / 6, 1));
                    } else {
                        $options->setWidth($options->getDefaultOption('width'));
                    }
                }
            }
            if ($column->getXlsColOptions()){
                $colOptions[$i] = $xls->addFormat($column->getXlsColOptions());
            } else {
                $colOptions[$i] = null;
            }
            if ($column->getXlsColHeaderOptions()){
                $colHeaderOptions[$i] = $xls->addFormat($column->getXlsColHeaderOptions());
            } else {
                $colHeaderOptions[$i] = null;
            }
            $sheet->setColumn($i, $i, $options->getWidth());

            $i++;
        }

        $headFormat = $xls->addFormat();
        $headFormat->setBold();
        $data = $this->_getExportData(Vps_Grid_Column::SHOW_IN_XLS);
        if (!is_null($data)) {
            foreach ($data as $row => $cols) {
                $i = 0;
                foreach ($cols as $col => $text) {
                    if (is_array($text)) $text = implode(', ', $text);
                    if ($row == 0) {
                        if ($colHeaderOptions[$i]) {
                            $format = $colHeaderOptions[$i];
                        } else {
                            $format = $headFormat;
                        }
                    } else {
                        $format = $colOptions[$i];
                    }
                    //TODO Zeilenumbrüche
                    $sheet->write($row, $col, mb_convert_encoding( $text, 'Windows-1252', 'UTF-8'), $format);
                    $i++;
                }
            }
        }
        $xls->close();
        $this->_helper->viewRenderer->setNoRender();
    }
}
