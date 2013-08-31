<?php
abstract class Kwf_Controller_Action_Auto_Grid extends Kwf_Controller_Action_Auto_Abstract
{
    /**
     * @var Kwf_Collection
     **/
    protected $_columns = null;
    protected $_buttons = array('save', 'add', 'delete');
    protected $_editDialog = null;
    protected $_paging = 0;
    protected $_defaultOrder;
    protected $_filters = null;
    protected $_queryFields; // deprecated, set in filterConfig
    protected $_querySeparator; // deprecated, set in filterConfig
    protected $_sortable = true; //ob felder vom user sortiert werden können
    protected $_position;
    protected $_csvExportCharset = 'UTF-8';

    protected $_primaryKey;
    /**
     * @var Kwf_Model_Interface
     **/
    protected $_model;
    protected $_table;     // deprecated: use models!
    protected $_tableName; // deprecated: use models!
    protected $_modelName; // deprecated: use model with a string
    protected $_grouping = null;

    protected $_pdf = array();

    protected $_progressBar = null;

    const PDF_ORIENTATION_PORTRAIT  = 'P';
    const PDF_ORIENTATION_LANDSCAPE = 'L';
    const PDF_FORMAT_A3 = 'A3';
    const PDF_FORMAT_A4 = 'A4';
    const PDF_EXPORTTYPE_TABLE = 1;
    const PDF_EXPORTTYPE_CONTAINER = 2;

    public function indexAction()
    {
        $this->view->controllerUrl = $this->getRequest()->getBaseUrl().$this->getRequest()->getPathInfo();
        if ($this->_getParam('componentId')) {
            $this->view->baseParams = array(
                'componentId' => $this->_getParam('componentId')
            );
        }
        $this->view->xtype = 'kwf.autogrid';
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
        $this->_columns = new Kwf_Collection();
        foreach ($addColumns as $k=>$column) {
            if (is_array($column)) {
                $columnObject = new Kwf_Grid_Column();
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
            $this->_model = Kwf_Model_Abstract::getInstance($this->_modelName);
        } else if (isset($this->_model) && is_string($this->_model)) {
            $this->_model = Kwf_Model_Abstract::getInstance($this->_model);
        }

        $filters = new Kwf_Controller_Action_Auto_FilterCollection();

        // Abwärtskompatibilität für Filterarray
        if (is_array($this->_filters)) {
            foreach ($this->_filters as $field => $config) {
                $filters->offsetSet($field, $config);
            }
        }
        $this->_filters = $filters;

        $this->_initColumns();

        // Abwärtskompatibilität falls Filterarray in initColumns gesetzt wurden
        if (is_array($this->_filters)) {
            $filters = new Kwf_Controller_Action_Auto_FilterCollection();
            foreach ($this->_filters as $field => $config) {
                $filters->offsetSet($field, $config);
            }
            $this->_filters = $filters;
        }

        $filters = is_array($this->_filters) ? $this->_filters : array();
        if ($this->_getParam('query') && !isset($this->_filters['text'])) {
            $this->_filters['text'] = true;
        }

        foreach ($this->_filters as $filter) {
            if ($this->_model) $filter->setModel($this->_model);

            // Abwärtskompatibilität für Textfilter mit queryFields und querySeparator
            if (!$filter instanceof Kwf_Controller_Action_Auto_Filter_Text) continue;
            if (!$filter->getProperty('queryFields', true)) {
                $queryFields = $this->_queryFields;
                if (!$queryFields) {
                    $queryFields = array();
                    foreach ($this->_columns as $column) {
                        $index = $column->getDataIndex();
                        if (!in_array($index, $this->_model->getColumns())) continue;
                        $queryFields[] = $index;
                    }
                }
                $info = $this->_getTableInfo();
                if ($info && $this->_primaryKey &&
                    !in_array($this->_primaryKey, $queryFields) &&
                    !in_array($info['name'].'.'.$this->_primaryKey, $queryFields)
                ) {
                    $queryFields[] = $this->_primaryKey;
                }
                $filter->setQueryFields($queryFields);
            }
            if ($this->_querySeparator) {
                $filter->setQuerySeparator($this->_querySeparator);
            }
        }

        if (isset($this->_model) && !isset($this->_primaryKey)) {
            $this->_primaryKey = $this->_model->getPrimaryKey();
        }

        if (isset($this->_model) && $this->_position && !isset($this->_columns[$this->_position])
            && in_array($this->_position, $this->_model->getColumns())
        ) {
            $columnObject = new Kwf_Grid_Column($this->_position);
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
                if (!$column->getType()) {
                    $column->setType((string)$this->_model->getColumnType($column->getDataIndex()));
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
                $columnObject = new Kwf_Grid_Column($this->_primaryKey);
                if (isset($this->_model)) {
                    $columnObject->setType((string)$this->_model->getColumnType($this->_primaryKey));
                } else {
                    // fallback
                    $columnObject->setType('string');
                }
                $this->_columns[] = $columnObject;
            }
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
            throw new Kwf_Exception("_getWhereQuery doesn't exist anymore");
        }

        // Falls Filter einen Default-Wert hat:
        // - GET query-Parameter setzen,
        // - Im JavaScript nach rechts verschieben und Defaultwert setzen
        foreach ($this->_filters as $filter) {
            if ($filter instanceof Kwf_Controller_Action_Auto_Filter_Text) continue;
            $param = $filter->getParamName();
            if ($filter->getDefault() && !$this->_getParam($param)) {
                $this->_setParam($param, $filter->getDefault());
            }
        }
    }

    public function setTable($table)
    {
        $this->_model = new Kwf_Model_Db(array(
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
            if ($d instanceof Kwf_Data_Table) {
                if (in_array($d->getField(), $exprColumns)) {
                    $ret->expr($d->getField());
                }
            }
        }

        // Filter
        foreach ($this->_filters as $filter) {
            if ($filter->getSkipWhere()) continue;
            $ret = $filter->formatSelect($ret, $this->_getAllParams());
        }

        $queryId = $this->getRequest()->getParam('queryId');
        if ($queryId) {
            $ret->where(new Kwf_Model_Select_Expr_Equal($this->_primaryKey, $queryId));
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

    protected function _getWhere()
    {
        return array();
    }

    protected function _fetchData($order, $limit, $start)
    {
        if (!isset($this->_model)) {
            throw new Kwf_Exception("Either _model has to be set or _fetchData has to be overwritten.");
        }

        $select = $this->_getSelect();
        if (is_null($select)) return null; //wenn _getSelect null zurückliefert nichts laden
        $select->limit($limit, $start);
        $order = $this->_getOrder($order);
        if ($order) $select->order($order);
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
        while ($m instanceof Kwf_Model_Proxy) {
            $m = $m->getProxyModel();
        }

        if (!isset($this->_model) || !($m instanceof Kwf_Model_Db)) {
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

    /**
    * This function is called when CONTROLLER_URL/json-data is called.
    */
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

// wird vermutlich nicht benötigt, da beim ersten laden 'sortInfo' in den metadaten drin ist
// falls es irgendwo benötigt wird wieder einkommentieren
//             $this->view->order = $order;
        }

        //TODO: dieser code sollte in _getOrder liegen
        $order = $this->_defaultOrder;
        if ($this->getRequest()->getParam('sort')) {
            $order['field'] = $this->getRequest()->getParam('sort');
        }
        if ($this->_getParam("direction") && $this->_getParam('direction') != 'undefined') {
            $order['direction'] = $this->_getParam('direction');
        }

        $primaryKey = $this->_primaryKey;

        $rowSet = $this->_fetchData($order, $limit, $start);
        if (!is_null($rowSet)) {
            if (isset($this->_paging['type']) && $this->_paging['type'] == 'Date') {
                //nothing to do, we don't know the total
            } else if ($this->_paging) {
                $this->view->total = $this->_fetchCount();
            } else {
                $this->view->total = sizeof($rowSet);
            }

            $number = 0;
            if ($start) $number = $start;
            $rows = array();
            foreach ($rowSet as $row) {
                $r = array();
                if (is_array($row)) {
                    $row = (object)$row;
                }
                if (!$this->_hasPermissions($row, 'load')) {
                    throw new Kwf_Exception("You don't have the permissions to load this row");
                }
                foreach ($this->_columns as $column) {
                    if ($column instanceof Kwf_Grid_Column_RowNumberer) continue;
                    if ($column->getShowIn() & Kwf_Grid_Column::SHOW_IN_GRID) {
                        $data = $column->load($row, Kwf_Grid_Column::ROLE_DISPLAY, array(
                                                    'number' => $number,
                                                    'total' => $this->view->total,
                                                ));
                        $r[$column->getDataIndex()] = $data;
                    }
                }
                if (!isset($r[$primaryKey]) && isset($row->$primaryKey)) {
                    $r[$primaryKey] = $row->$primaryKey;
                }
                $rows[] = $r;
                $number++;
            }

            $this->view->rows = $rows;
        } else {
            $this->view->total = 0;
            $this->view->rows = array();
        }

        if ($this->getRequest()->getParam('meta')) {
            $this->_appendMetaData();
        }
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
            if (!($column->getShowIn() & Kwf_Grid_Column::SHOW_IN_GRID)) continue;
            $data = $column->getMetaData($this->_getModel(), $this->_getTableInfo());
            if ($data) {
                $this->view->metaData['columns'][] = $data;
                if ($column instanceof Kwf_Grid_Column_RowNumberer) continue;

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
        foreach ($this->_filters as $filter) {
            $filters[] = $filter->getExtConfig();
        }
        $this->view->metaData['filters'] = $filters;
        $this->view->metaData['sortable'] = $this->_sortable;
        $this->view->metaData['editDialog'] = $this->_editDialog;
        $this->view->metaData['grouping'] = $this->_grouping;
    }

    protected function _hasPermissions($row, $action)
    {
        return true;
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row, $submitRow)
    {
    }

    protected function _afterSave(Kwf_Model_Row_Interface $row, $submitRow)
    {
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row, $submitRow)
    {
    }

    protected function _afterInsert(Kwf_Model_Row_Interface $row, $submitRow)
    {
    }

    protected function _beforeDelete(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _afterDelete()
    {
    }

    protected function _getRowById($id)
    {
        if ($id) {
            $s = $this->_getSelect(); //use same select as for loading to be able to save only rows we can view
            $s->whereId($id);
            $row = $this->_model->getRow($s);
        } else {
            if (!isset($this->_permissions['add']) || !$this->_permissions['add']) {
                throw new Kwf_Exception("Add is not allowed.");
            }
            $row = $this->_model->createRow();
        }
        return $row;
    }

    public function jsonSaveAction()
    {
        if (!isset($this->_permissions['save']) || !$this->_permissions['save']) {
            throw new Kwf_Exception("Save is not allowed.");
        }
        $success = false;

        $data = Zend_Json::decode($this->getRequest()->getParam("data"));
        if (!$data) $data = array();
        $addedIds = array();
        ignore_user_abort(true);
        if (Zend_Registry::get('db')) Zend_Registry::get('db')->beginTransaction();
        foreach ($data as $submitRow) {
            $id = $submitRow[$this->_primaryKey];
            $row = $this->_getRowById($id);
            if (!$row) {
                throw new Kwf_Exception("Can't find row with id '$id'.");
            }
            if (!$this->_hasPermissions($row, 'save')) {
                throw new Kwf_Exception("You don't have the permissions to save this row.");
            }
            foreach ($this->_columns as $column) {
                if (!($column->getShowIn() & Kwf_Grid_Column::SHOW_IN_GRID)) continue;
                $invalid = $column->validate($row, $submitRow);
                if ($invalid) {
                    $invalid = Kwf_Form::formatValidationErrors($invalid);
                    throw new Kwf_Exception_Client(implode("<br />", $invalid));
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
        if (Zend_Registry::get('db')) Zend_Registry::get('db')->commit();
        $success = true;

        if ($addedIds) {
            $this->view->addedIds = $addedIds;
        }
        $this->view->success = $success;
    }

    public function jsonDeleteAction()
    {
        if (!isset($this->_permissions['delete']) || !$this->_permissions['delete']) {
            throw new Kwf_Exception("Delete is not allowed.");
        }
        $ids = $this->getRequest()->getParam($this->_primaryKey);
        $ids = explode(';', $ids);

        ignore_user_abort(true);
        if (Zend_Registry::get('db')) Zend_Registry::get('db')->beginTransaction();
        foreach ($ids as $id) {
            $row = $this->_model->find($id)->current();
            if (!$row) {
                throw new Kwf_Exception_Client("Can't find row with id '$id'.");
            }
            if (!$this->_hasPermissions($row, 'delete')) {
                throw new Kwf_Exception("You don't have the permissions to delete this row.");
            }
            $this->_beforeDelete($row);
            $this->_deleteRow($row);
            $this->_afterDelete();
        }
        if (Zend_Registry::get('db')) Zend_Registry::get('db')->commit();
    }

    //kann ueberschrieben werden um zB deleted=1 zu setzen statt echt zu loeschen
    protected function _deleteRow(Kwf_Model_Row_Interface $row)
    {
        $row->delete();
    }

    public function jsonDuplicateAction()
    {
        if (!isset($this->_permissions['duplicate']) || !$this->_permissions['duplicate']) {
            throw new Kwf_Exception("Duplicate is not allowed.");
        }
        $ids = $this->getRequest()->getParam($this->_primaryKey);
        $ids = explode(';', $ids);

        $this->view->data = array('duplicatedIds' => array());
        ignore_user_abort(true);
        if (Zend_Registry::get('db')) Zend_Registry::get('db')->beginTransaction();
        foreach ($ids as $id) {
            $row = $this->_model->getRow($id);
            if (!$row) {
                throw new Kwf_Exception("Can't find row with id '$id'.");
            }
            if (!$this->_hasPermissions($row, 'duplicate')) {
                throw new Kwf_Exception("You don't have the permissions to duplicate this row.");
            }
            $new = $row->duplicate();
            $this->view->data['duplicatedIds'][] = $new->{$this->_primaryKey};
        }
        if (Zend_Registry::get('db')) Zend_Registry::get('db')->commit();
    }

    public function pdfAction()
    {
        if (!isset($this->_permissions['pdf']) || !$this->_permissions['pdf']) {
            throw new Kwf_Exception("Pdf is not allowed.");
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
                if (!($column->getShowIn() & Kwf_Grid_Column::SHOW_IN_PDF)) continue;
                if ($column->getHeader()) {
                    $this->_pdf['fields'][] = $column->getName();
                }
            }
        }

        if (!is_array($this->_pdf['fields'])) {
            throw new Kwf_Exception("PDF export fields must be of type `array`");
        }
        if (isset($this->_pdf['columns'])) {
            throw new Kwf_Exception("PDF export fields key is labeld `fields`, not `columns`");
        }
        $tmpFields = array(); // Needed for correct sorting
        foreach ($this->_pdf['fields'] as $key => $mixed) {
            if (!is_array($mixed) && !is_string($mixed)) {
                throw new Kwf_Exception("PDF export field `$mixed` must not be of type "
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
            $pdf = new Kwf_Grid_Pdf_Table($this->_pdf['orientation'], 'mm', $this->_pdf['format']);
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

    private function _getExportData($onlyShowIn, $calcEstimatedMemUsageType, $memoryLimitMb = 0)
    {
        if (!isset($this->_model)) {
            $rowSet = $this->_fetchData(null, null, null);
            $countRows = count($rowSet);
        } else {
            $sel = $this->_getSelect();
            if (is_null($sel)) return array();

            //TODO: dieser code sollte in _getOrder liegen
            $order = $this->_defaultOrder;
            if ($this->getRequest()->getParam('sort')) {
                $order['field'] = $this->getRequest()->getParam('sort');
            }
            if ($this->_getParam("direction") && $this->_getParam('direction') != 'undefined') {
                $order['direction'] = $this->_getParam('direction');
            }
            $order = $this->_getOrder($order);
            if ($order) $sel->order($order);

            $countRows = $this->_model->countRows($sel);
            $rowSet = $this->_model->getRows($sel);
        }

        if ($rowSet && $countRows) {
            $this->_progressBar = new Zend_ProgressBar(
                new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum')),
                0, ($countRows * 1.05) * 3
            );

            // Index 0 reserved for column headers
            $exportData = array(0 => array());

            $estimatedMemoryUsage = memory_get_usage();
            $memForRows = array();
            $rowLenghtes = array();

            $columns = $columnsHeader = array();
            foreach ($rowSet as $row) {
                $rowBeginMemUsage = memory_get_usage();

                if (is_array($row)) { // wenn _fetchData() überschrieben wurde
                    $row = (object)$row;
                }
                if (!$this->_hasPermissions($row, 'load')) {
                    throw new Kwf_Exception("You don't have the permissions to load this row");
                }
                $columns = $columnsHeader = array();
                foreach ($this->_columns as $column) {
                    if (!($column->getShowIn() & $onlyShowIn)) continue;
                    $currentColumnHeader = $column->getHeader();

                    if (!is_null($currentColumnHeader)) {
                        $columnsHeader[] = (string)$currentColumnHeader;
                        $colVal = $column->load($row, Kwf_Grid_Column::ROLE_EXPORT, array());
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
                        if ($setTypeTo == 'bool') {
                            if ($colVal) {
                                $colVal = trlKwf('Yes');
                            } else {
                                $colVal = trlKwf('No');
                            }
                        } else {
                            settype($colVal, $setTypeTo);
                        }
                        $columns[] = $colVal;
                    }
                }
                $exportData[] = $columns;

                // zum berechnen des geschätzten speicherverbrauchs
                if ($memoryLimitMb) {
                    if (count($rowLenghtes) == 40) {
                        // text length
                        $estimatedMemoryUsage += (array_sum($rowLenghtes) / count($rowLenghtes)) * $countRows;

                        // daten sammeln in dieser schleife hier
                        $estimatedMemoryUsage += (array_sum($memForRows) / count($memForRows)) * $countRows;

                        // xls export
                        if ($calcEstimatedMemUsageType == 'xls') {
                            $estimatedMemoryUsage += 1400 * $countRows * count($columns);
                        }
                        /**
                         * TODO: Calculating for csv
                         */

                        if (($estimatedMemoryUsage / 1024) / 1024 > $memoryLimitMb) {
                            throw new Kwf_Exception_Client(trlKwf("Too many rows to export. Try exporting two times with fewer rows."));
                        }
                    }
                    if (count($rowLenghtes) < 41) {
                        $memForRows[] = (memory_get_usage() - $rowBeginMemUsage);
                        $rowLenghtes[] = strlen(implode('', $columns));
                    }
                }

                $this->_progressBar->next(2, trlKwf('Collecting data'));
            }
            $exportData[0] = $columnsHeader;
            return $exportData;
        } else {
            $this->_progressBar = new Zend_ProgressBar(
                new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum')),
                0, 4
            );
        }
        return array();
    }

    public function jsonCsvAction()
    {
        if (!isset($this->_permissions['csv']) || !$this->_permissions['csv']) {
            throw new Kwf_Exception("CSV is not allowed.");
        }

        ini_set('memory_limit', "384M");
        set_time_limit(600); // 10 minuten

        $data = $this->_getExportData(Kwf_Grid_Column::SHOW_IN_CSV, 'csv', 0);

        if (!is_null($data)) {
            $csvRows = array();
            foreach ($data as $row => $cols) {
                $cols = str_replace('"', '""', $cols);
                $importCols = array();
                if ($this->_csvExportCharset != 'UTF-8') {
                    foreach ($cols as $col) {
                        $col = mb_convert_encoding($col, $this->_csvExportCharset, 'UTF-8');
                        $importCols[] = $col;
                    }
                } else {
                    $importCols = $cols;
                }
                $csvRows[] = '"'. implode('";"', $importCols) .'"';
                $this->_progressBar->next(1, trlKwf('Writing data'));
            }

            $csvReturn = implode("\r\n", $csvRows);
        }

        $downloadkey = uniqid();
        file_put_contents('temp/'.$downloadkey.'.csv', $csvReturn);

        $this->_progressBar->finish();

        $this->view->downloadkey = $downloadkey;
    }

    public function downloadCsvExportFileAction()
    {
        if (!isset($this->_permissions['csv']) || !$this->_permissions['csv']) {
            throw new Kwf_Exception("CSV is not allowed.");
        }
        if (!file_exists('temp/'.$this->_getParam('downloadkey').'.csv')) {
            throw new Kwf_Exception('Wrong downloadkey submitted');
        }

        Kwf_Util_TempCleaner::clean();

        $file = array(
            'contents' => file_get_contents('temp/'.$this->_getParam('downloadkey').'.csv'),
            'mimeType' => 'application/octet-stream',
            'downloadFilename' => 'export_'.date('Ymd-Hi').'.csv'
        );
        Kwf_Media_Output::output($file);
        $this->_helper->viewRenderer->setNoRender();
    }

    protected function _getColumnLetterByIndex($idx)
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

    public function jsonXlsAction()
    {
        // e.g.: Stargate customer export: 128M memory_limit exhaust at 1500 lines
        ini_set('memory_limit', "768M");
        set_time_limit(600); // 10 minuten
        if (!isset($this->_permissions['xls']) || !$this->_permissions['xls']) {
            throw new Kwf_Exception("XLS is not allowed.");
        }

        $data = $this->_getExportData(Kwf_Grid_Column::SHOW_IN_XLS, 'xls', 640);

        require_once Kwf_Config::getValue('externLibraryPath.phpexcel').'/PHPExcel.php';
        $xls = new PHPExcel();
        $xls->getProperties()->setCreator("Vivid Planet Software GmbH");
        $xls->getProperties()->setLastModifiedBy("Vivid Planet Software GmbH");
        $xls->getProperties()->setTitle("KWF Excel Export");
        $xls->getProperties()->setSubject("KWF Excel Export");
        $xls->getProperties()->setDescription("KWF Excel Export");
        $xls->getProperties()->setKeywords("KWF Excel Export");
        $xls->getProperties()->setCategory("KWF Excel Export");

        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();

        // setting width for each column
        $colIndex = 0;
        $renderer = array();
        foreach ($this->_columns as $column) {
            if (!($column->getShowIn() & Kwf_Grid_Column::SHOW_IN_XLS)) continue;
            if (is_null($column->getHeader())) continue;

            if ($column->getXlsWidth()) {
                $width = $column->getXlsWidth();
            } else if ($column->getWidth()) {
                $width = round($column->getWidth() / 6, 1);
            } else {
                $width = 15;
            }

            $sheet->getColumnDimension($this->_getColumnLetterByIndex($colIndex))->setWidth($width);
            $renderer[$colIndex] = $column->getRenderer();
            $colIndex++;
        }

        $helperDate = new Kwf_View_Helper_Date();
        $helperDateTime = new Kwf_View_Helper_DateTime();
        foreach ($data as $row => $cols) {
            // row ist index, das andre nicht, passt aber trotzdem so
            // da ja in der ersten Zeile der Header steht
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

                // datum umformatieren
                if (strlen($text) == 10 && preg_match('/^[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2}$/', $text)) {
                    $text = $helperDate->date($text);
                } else if (strlen($text) == 19 && preg_match('/^[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2} [0-9]{2,2}:[0-9]{2,2}:[0-9]{2,2}$/', $text)) {
                    $text = $helperDateTime->dateTime($text);
                }
                $sheet->setCellValueExplicit($cell, $text, $cellType);
                if ($renderer[$col] == 'clickableLink') {
                    $sheet->getCell($cell)->getHyperlink()->setUrl($text);
                }
            }

            $this->_progressBar->next(1, trlKwf('Writing data. Please be patient.'));
        }
        // write the file
        $objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel5');
        $downloadkey = uniqid();
        $objWriter->save('temp/'.$downloadkey.'.xls');

        $this->_progressBar->finish();

        $this->view->downloadkey = $downloadkey;
    }

    public function downloadExportFileAction()
    {
        if (!isset($this->_permissions['xls']) || !$this->_permissions['xls']) {
            throw new Kwf_Exception("XLS is not allowed.");
        }
        if (!file_exists('temp/'.$this->_getParam('downloadkey').'.xls')) {
            throw new Kwf_Exception('Wrong downloadkey submitted');
        }
        Kwf_Util_TempCleaner::clean();

        $file = array(
            'contents' => file_get_contents('temp/'.$this->_getParam('downloadkey').'.xls'),
            'mimeType' => 'application/msexcel',
            'downloadFilename' => 'export_'.date('Ymd-Hi').'.xls'
        );
        Kwf_Media_Output::output($file);
        $this->_helper->viewRenderer->setNoRender();
    }

    // deprecated, statt dessen Filter überschreiben!
    protected final function _getQueryExpression($query) {}
}
