<?php
class Kwf_Rest_Controller_Model extends Kwf_Rest_Controller
{
    protected $_model;
    protected $_saveColumns;
    protected $_loadColumns;
    protected $_queryColumns;

    public function preDispatch()
    {
        parent::preDispatch();
        if (is_string($this->_model)) $this->_model = Kwf_Model_Abstract::getInstance($this->_model);
    }

    protected function _getSelect()
    {
        return $this->_model->select();
    }

    protected function _getSelectIndex()
    {
        $ret = $this->_getSelect();
        $filter = $this->_getParam('filter');
        if ($filter) {
            $filter = json_decode($filter);
        } else {
            $filter = array();
        }
        $this->_applySelectFilters($ret, $filter);

        $query = $this->_getParam('query');
        if ($query) {
            $this->_applySelectQuery($ret, $query);
        }

        $sort = $this->_getParam('sort');
        if ($sort) {
            $this->_applySelectSort($ret, json_decode($sort));
        }

        if ($this->_getParam('limit')) {
            $ret->limit($this->_getParam('limit'), $this->_getParam('start'));
        }
        return $ret;
    }

    protected function _applySelectSort($select, array $sort)
    {
        foreach ($sort as $s) {
            $select->order($s->property, $s->direction);
        }
    }

    protected function _applySelectFilters($select, array $filters)
    {
        foreach ($filters as $f) {
            $this->_applySelectFilter($select, $f);
        }
    }

    protected function _applySelectFilter($select, $filter)
    {
        if ($filter->property == 'query') {
            $this->_applySelectQuery($select, $filter->value);
        } else {
            if (!is_null($filter->value)) {
                $select->whereEquals($filter->property, $filter->value);
            }
        }
    }

    protected function _hasFilterParam($filterName)
    {
        $ret = false;
        $filter = $this->_getParam('filter');
        if ($filter) {
            $filter = json_decode($filter);
            foreach ($filter as $f) {
                if ($f->property == $filterName) {
                    $ret = true;
                    break;
                }
            }
        }
        return $ret;
    }

    protected function _applySelectQuery($select, $query)
    {
        $query = trim($query);
        if (!$query) return;
        $ors = array();
        if (!$this->_queryColumns) {
            throw new Kwf_Exception("_queryColumns are required");
        }
        foreach ($this->_queryColumns as $c) {
            $ors[] = new Kwf_Model_Select_Expr_Like($c, '%'.$query.'%');
        }
        $select->where(new Kwf_Model_Select_Expr_Or($ors));
    }

    protected function _loadDataFromRow($row)
    {
        $data = $row->toArray();
        if ($this->_loadColumns) {
            foreach ($this->_loadColumns as $c) {
                $data[$c] = $row->$c;
            }
        }
        return $data;
    }

    /**
     * The head action handles HEAD requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function headAction()
    {
    }
    // Handle GET and return a list of resources
    public function indexAction()
    {
        $this->view->data = array();
        $s = $this->_getSelectIndex();
        if ($this->_loadColumns) {
            foreach ($this->_loadColumns as $c) {
                $s->expr($c);
            }
        }
        foreach ($this->_model->getRows($s) as $row) {
            $this->view->data[] = $this->_loadDataFromRow($row);
        }
        $this->view->total = $this->_model->countRows($s);
    }

    // Handle GET and return a specific resource item
    public function getAction()
    {
        $s = $this->_getSelect();
        if ($this->_loadColumns) {
            foreach ($this->_loadColumns as $c) {
                $s->expr($c);
            }
        }
        $s->whereId($this->_getParam('id'));
        $row = $this->_model->getRow($s);
        if (!$row) throw new Kwf_Exception_NotFound();
        $this->view->data = $this->_loadDataFromRow($row);
    }

    // Handle POST requests to create a new resource item
    public function postAction()
    {
        $data = json_decode($this->getRequest()->getRawBody());

        $row = $this->_model->createRow();
        if ($this->_getParam('id')) {
            $row->id = $this->_getParam('id');
        }

        $this->_fillRowInsert($row, $data);
        $this->_beforeInsert($row);
        $this->_beforeSave($row);
        $row->save();
        $this->_afterSave($row, $data);
        $this->_afterInsert($row, $data);

        $this->view->data = $this->_loadDataFromRow($row);
    }

    protected function _fillRowInsert($row, $data)
    {
        $this->_fillRow($row, $data);
    }

    protected function _fillRow($row, $data)
    {
        foreach ($this->_saveColumns as $col) {
            if (!property_exists($data, $col)) continue;
            $v = $data->$col;
            if (!is_null($v) && $this->_model->getColumnType($col) == Kwf_Model_Interface::TYPE_DATE) {
                $v = new Kwf_Date($v);
                $v = $v->format();
            } else if (!is_null($v) && $this->_model->getColumnType($col) == Kwf_Model_Interface::TYPE_DATETIME) {
                $v = new Kwf_DateTime($v);
                $v = $v->format();
            }
            $row->$col = $v;
        }
    }

    // Handle PUT requests to update a specific resource item
    public function putAction()
    {
        $data = json_decode($this->getRequest()->getRawBody());

        $s = $this->_getSelect();
        $s->whereId($this->_getParam('id'));
        $row = $this->_model->getRow($s);
        if (!$row) throw new Kwf_Exception_NotFound();

        $this->_fillRow($row, $data);
        $this->_beforeUpdate($row);
        $this->_beforeSave($row);
        $row->save();
        $this->_afterSave($row, $data);
        $this->_afterUpdate($row, $data);

        $this->view->data = $this->_loadDataFromRow($row);
    }

    // Handle DELETE requests to delete a specific item
    public function deleteAction()
    {
        $s = $this->_getSelect();
        $s->whereId($this->_getParam('id'));
        $row = $this->_model->getRow($s);
        if (!$row) throw new Kwf_Exception_NotFound();
        $this->_beforeDelete($row);
        $row->delete();
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _beforeUpdate(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _beforeDelete(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _afterUpdate(Kwf_Model_Row_Interface $row, $data)
    {
    }

    protected function _afterInsert(Kwf_Model_Row_Interface $row, $data)
    {
    }

    protected function _afterSave(Kwf_Model_Row_Interface $row, $data)
    {
    }
}
