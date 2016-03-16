<?php
class Kwf_Rest_Controller_Model extends Kwf_Rest_Controller
{
    protected $_model;
    protected $_saveColumns;
    protected $_loadColumns;
    protected $_queryColumns;
    protected $_querySplit = false;

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

    protected function _getFilterParam($filterName)
    {
        $ret = null;
        $filter = $this->_getParam('filter');
        if ($filter) {
            $filter = json_decode($filter);
            foreach ($filter as $f) {
                if ($f->property == $filterName && $f->value) {
                    $ret = $f->value;
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

        $exprs = array();
        if (!$this->_queryColumns) {
            throw new Kwf_Exception("_queryColumns are required");
        }

        if ($this->_querySplit) {
            $query = explode(' ', $query);
        } else {
            $query = array($query);
        }

        foreach ($query as $q) {
            $e = array();
            foreach ($this->_queryColumns as $c) {
                $e[] = new Kwf_Model_Select_Expr_Like($c, '%'.$q.'%');
            }

            if (count($e) > 1) {
                $exprs[] = new Kwf_Model_Select_Expr_Or($e);
            } else {
                $exprs[] = $e[0];
            }
        }

        if (count($exprs) > 1) {
            $select->where(new Kwf_Model_Select_Expr_And($exprs));
        } else {
            $select->where($exprs[0]);
        }
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
        if (!is_array($data)) $data = array($data);

        $ret = array();
        foreach ($data as $d) {
            $row = $this->_model->createRow();
            if (isset($d->id) && $d->id) {
                $row->id = $d->id;
            }

            $this->_fillRowInsert($row, $d);
            $this->_beforeInsert($row);
            $this->_beforeSave($row);
            $row->save();
            $this->_afterSave($row, $d);
            $this->_afterInsert($row, $d);

            $ret[] = $this->_loadDataFromRow($row);
        }
        if (count($ret) == 1) $ret = reset($ret);

        $this->view->data = $ret;
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
        if (!is_array($data)) $data = array($data);

        $ret = array();
        foreach ($data as $d) {
            $s = $this->_getSelect();
            $s->whereId($d->id);
            $row = $this->_model->getRow($s);
            if (!$row) throw new Kwf_Exception_NotFound();

            $this->_fillRow($row, $d);
            $this->_beforeUpdate($row);
            $this->_beforeSave($row);
            $row->save();
            $this->_afterSave($row, $d);
            $this->_afterUpdate($row, $d);

            $ret[] = $this->_loadDataFromRow($row);
        }
        if (count($ret) == 1) $ret = reset($ret);

        $this->view->data = $ret;
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
