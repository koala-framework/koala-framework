<?php
abstract class Vps_Controller_Action_Auto_Grid extends Vps_Controller_Action_Auto_Abstract
{
    protected $_columns = null;
    protected $_buttons = array('save'=>true,
                                'add'=>true,
                                'delete'=>true,
                                'reload'=>true);
    protected $_editDialog = null;
    protected $_paging = 0;
    protected $_defaultOrder;
    protected $_filters = array();
    protected $_queryFields;
    protected $_sortable = true;
    protected $_position;

    protected $_primaryKey;
    protected $_table;
    protected $_tableName;

    protected $_grouping = null;

    public function indexAction()
    {
       $this->view->ext('Vps.Auto.GridPanel');
    }

    public function jsonIndexAction()
    {
       $this->indexAction();
    }

    protected function _initColumns()
    {
    }
    public function init()
    {
        parent::init();

        if (!isset($this->_table) && isset($this->_tableName)) {
            $this->_table = new $this->_tableName();
        }

        if (isset($this->_table)) {
            $info = $this->_table->info();
            if(!isset($this->_primaryKey)) {
                $info = $this->_table->info();
                $this->_primaryKey = $info['primary'];
            }
        }

        $addColumns = array();
        if (is_array($this->_columns)) $addColumns = $this->_columns;
        $this->_columns = new Vps_Collection();
        foreach ($addColumns as $k=>$column) {
            if (is_array($column)) {
                $columnObject = new Vps_Auto_Grid_Column();
                foreach ($column as $propName => $propValue) {
                    $columnObject->setProperty($propName, $propValue);
                }
                $this->_columns[] = $columnObject;
            } else {
                $this->_columns[] = $column;
            }
        }
        $this->_initColumns();

        if (is_array($this->_primaryKey)) {
            $this->_primaryKey = $this->_primaryKey[1];
        }

        if (isset($this->_table)) {
            $info = $this->_getTableInfo();
            if ($this->_position && array_search($this->_position, $info['cols'])) {
                $columnObject = new Vps_Auto_Grid_Column($this->_position);
                $columnObject->setHeader(' ')
                             ->setWidth(30)
                             ->setType('int')
                             ->setEditor('PosField');
                $this->_columns->prepend($columnObject);
                $this->_sortable = false;
                $this->_defaultOrder = $this->_position;
            }
            $primaryFound = false;
            foreach ($this->_columns as $column) {
                if (!$column->getType() && isset($info['metadata'][$column->getDataIndex()])) {
                    $column->setType($this->_getTypeFromDbType($info['metadata'][$column->getDataIndex()]['DATA_TYPE']));
                }
                if ($column->getDataIndex() == $this->_primaryKey) {
                    $primaryFound = true;
                }
            }
            if (!$primaryFound) {
                //primary key hinzufügen falls er noch nicht in gridColumns existiert
                $columnObject = new Vps_Auto_Grid_Column($this->_primaryKey);
                $columnObject->setType($this->_getTypeFromDbType($info['metadata'][$this->_primaryKey]['DATA_TYPE']));
                $this->_columns[] = $columnObject;
            }
        }

        //default durchsucht alle angezeigten felder
        if (!isset($this->_queryFields)) {
            $this->_queryFields = array();
            foreach ($this->_columns as $column) {
                $index = $column->getDataIndex();
                if (isset($this->_table)) {
                    $info = $this->_getTableInfo();
                    if (!isset($info['metadata'][$index])) continue;
                }
                $this->_queryFields[] = $index;
            }
        }

        if ($this->_sortable && !isset($this->_defaultOrder)) {
            $this->_defaultOrder = $this->_columns->first()->getDataIndex();
        }

        if (is_string($this->_defaultOrder)) {
            $o = $this->_defaultOrder;
            $this->_defaultOrder = array();
            $this->_defaultOrder['field'] = $o;
            $this->_defaultOrder['direction'] = 'ASC';
        }
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
        foreach($this->_filters as $field=>$filter) {
            if($field=='text') continue; //handled above
            if ($this->_getParam('query_'.$field)) {
                $where[$field.' = ?'] = $this->_getParam('query_'.$field);
            }
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

    private function _getTableInfo()
    {
        if (!isset($this->_table)) return null;
        return $this->_table->info();
    }

    protected function _fetchCount()
    {
        if (!isset($this->_table)) {
            throw new Vps_Exception("Either _gridTable has to be set or _fetchData has to be overwritten.");
        }
        $select = $this->_table->getAdapter()->select();
        $info = $this->_getTableInfo();

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
            $limit = $this->getRequest()->getParam('limit');
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
        $order = $this->getRequest()->getParam('sort');
        if (!$order) $order = $this->_defaultOrder['field'];
        if($this->_getParam("dir") && $this->_getParam('dir')!='UNDEFINED') {
            $order .= ' '.$this->_getParam('dir');
        } else {
            $order .= ' '.$this->_defaultOrder['direction'];
        }
        $order = trim($order);
        $this->view->order = $order;

        $primaryKey = $this->_primaryKey;

        $rowSet = $this->_fetchData($order, $limit, $start);
        if (!is_null($rowSet)) {
            $rows = array();
            foreach ($rowSet as $row) {
                $r = array();
                if (is_array($row)) {
                    $row = (object)$row;
                }
                foreach ($this->_columns as $column) {
                    $data = $column->load($row, Vps_Auto_Grid_Column::ROLE_DISPLAY);
                    $r[$column->getDataIndex()] = $data;
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
        else if ($type == 'decimal') $type = 'float';
        else if (substr($type, 0, 6) == 'double') $type = 'float';
        else if ($type == 'time') $type = ''; //auto
        else $type = ''; //auto
        return $type;
    }

    protected function _appendMetaData()
    {
        $this->view->metaData = array();

        $this->view->metaData['root'] = 'rows';
        $this->view->metaData['id'] = $this->_primaryKey;
        if (isset($this->_paging['type']) && $this->_paging['type'] == 'Date') {
            //nix zu tun
        } else {
            $this->view->metaData['totalProperty'] = 'total';
        }
        $this->view->metaData['successProperty'] = 'success';
        if ($this->_sortable && !$this->_getParam('sort')) {
            //sandard-sortierung
            $this->view->metaData['sortInfo'] = $this->_defaultOrder;
        } else if ($this->_sortable) {
            $this->view->metaData['sortInfo']['field'] = $this->_getParam('sort');
            $this->view->metaData['sortInfo']['direction'] = $this->_getParam('dir');
        }
        $this->view->metaData['columns'] = array();
        $this->view->metaData['fields'] = array();
        foreach ($this->_columns as $column) {
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
        $this->view->metaData['paging'] = $this->_paging;
        $this->view->metaData['filters'] = $this->_filters;
        $this->view->metaData['sortable'] = $this->_sortable;
        $this->view->metaData['editDialog'] = $this->_editDialog;
        $this->view->metaData['grouping'] = $this->_grouping;
    }

    protected function _beforeSave(Zend_Db_Table_Row_Abstract $row, $submitRow)
    {
    }

    protected function _afterSave(Zend_Db_Table_Row_Abstract $row, $submitRow)
    {
    }

    protected function _beforeInsert(Zend_Db_Table_Row_Abstract $row, $submitRow)
    {
    }

    protected function _afterInsert(Zend_Db_Table_Row_Abstract $row, $submitRow)
    {
    }

    protected function _beforeDelete(Zend_Db_Table_Row_Abstract $row)
    {
    }

    protected function _afterDelete()
    {
    }

    public function jsonSaveAction()
    {
        if(!isset($this->_permissions['save']) || !$this->_permissions['save']) {
            throw new Vps_Exception("Save is not allowed.");
        }
        $success = false;

        $data = Zend_Json::decode($this->getRequest()->getParam("data"));
        $addedIds = array();
        foreach ($data as $submitRow) {
            $id = $submitRow[$this->_primaryKey];
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
            foreach ($this->_columns as $column) {
                if ($id && $column->getDataIndex() == $this->_position) {
                    $row->numberize($this->_position, $submitRow[$this->_position], $this->_getWhere());
                } else {
                    $column->prepareSave($row, $submitRow);
                }
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
                if ($this->_position) {
                    $row->numberize($this->_position, $submitRow[$this->_position], $this->_getWhere());
                }
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
            $this->_beforeDelete($row);
            $row->delete();
            $this->_afterDelete();
            if ($this->_position) {
                $this->_table->numberizeAll($this->_position, $this->_getWhere());
            }
            $success = true;
        } catch (Vps_ClientException $e) { //todo: nicht nur Vps_Exception fangen
            $this->view->error = $e->getMessage();
        }

        $this->view->success = $success;
    }


    public function pdfAction()
    {
        if(!isset($this->_permissions['pdf']) || !$this->_permissions['pdf']) {
            throw new Vps_Exception("Pdf is not allowed.");
        }
        $pdf = new Zend_Pdf();

        $pdf->pages[] = $pdfPage = new Vps_Pdf_Page(Zend_Pdf_Page::SIZE_A4);

        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        $pdfPage->setFont($font, 11);

        $pageMargin = 20;
        $padding = 5;

        $numColumnsAutoWidth = 0;
        $remainingAutoWidth = $pdfPage->getWidth() - $pageMargin * 2;
        foreach ($this->_columns as $column) {
            if (!$column->getPdfWidth()) {
                $numColumnsAutoWidth++;
            } else {
                $remainingAutoWidth -= $column->getPdfWidth() + $padding * 2;
            }
        }

        $pdfPage->setLineWidth(0.1);
        $x = $pageMargin;
        foreach ($this->_columns as $column) {
            if (!$column->getPdfWidth()) {
                $w = $remainingAutoWidth / $numColumnsAutoWidth - $padding * 2;
                $column->setPdfWidth($w);
            } else {
                $w = $column->getPdfWidth();
            }
            $h = $pdfPage->getTextHeight();
            $text = $column->getHeader();
            while ($pdfPage->getTextWidth($text) > $w) {
                $text = substr(rtrim($text, '.'), 0, -1) . '...';
            }
            $pdfPage->drawText($text, $x+$padding, $pdfPage->getHeight()-$pageMargin-$padding, 'utf8');
            $pdfPage->drawRectangle($x, $pdfPage->getHeight()-$pageMargin-$padding*2, $x+$w+$padding*2, $pdfPage->getHeight()-$pageMargin+$h-$padding, Zend_Pdf_Page::SHAPE_DRAW_STROKE);
            $x += $w + $padding * 2;
        }


        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $pdfPage->setFont($font, 11);

        $order = trim($this->_defaultOrder['field'].' '.$this->_defaultOrder['direction']);
        $rowSet = $this->_fetchData($order, null, null);

        if (!is_null($rowSet)) {
            $rows = array();
            $y = $pdfPage->getHeight() - $pageMargin - $padding*3 - $pdfPage->getTextHeight();
            foreach ($rowSet as $row) {
                if (is_array($row)) {
                    $row = (object)$row;
                }
                $x = $pageMargin;
                $minY = $pdfPage->getHeight();
                foreach ($this->_columns as $column) {
                    $text = $column->load($row, Vps_Auto_Grid_Column::ROLE_PDF);
                    $w = $column->getPdfWidth();
                    $o = array('wrap'        => Vps_Pdf_Page::OPTIONS_WRAP_ENABLED,
                               'wrap-indent' => $x+$padding,
                               'wrap-pad'    => $pdfPage->getWidth() - ($x + $padding*2 + $w)
                               );
                    $yText = $pdfPage->drawText($text, $x+$padding, $y, 'utf8', $o);
                    if ($yText < $minY) $minY = $yText;
                    $x += $w + $padding * 2;
                }
                $x = $pageMargin;
                foreach ($this->_columns as $column) {
                    $w = $column->getPdfWidth();
                    $pdfPage->drawRectangle($x, $minY-$padding*1, $x+$w+$padding*2, $y+$padding*2, Zend_Pdf_Page::SHAPE_DRAW_STROKE);
                    $x += $w + $padding * 2;
                }
                $y = $minY - $padding*3;
            }
        }

        $this->_helper->viewRenderer->setNoRender();
        $this->getResponse()->setHeader('Content-Type', 'application/pdf')
                            ->setBody($pdf->render());
    }
}
