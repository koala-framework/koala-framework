<?php
class Kwf_Rest_Controller_Model extends Zend_Rest_Controller
{
    protected $_model;
    protected $_saveColumns;
    protected $_loadColumns;
    protected $_queryColumns;

    public function preDispatch()
    {
        parent::preDispatch();

        Kwf_Util_Https::ensureHttps();

        if (is_string($this->_model)) $this->_model = Kwf_Model_Abstract::getInstance($this->_model);

        if ($this->_getParam('applicationAssetsVersion')) {
            if (Kwf_Assets_Dispatcher::getAssetsVersion() != $this->_getParam('applicationAssetsVersion')) {
                $this->_forward('json-wrong-version', 'error',
                                    'kwf_controller_action_error');
                return;
            }
        }

        if (Kwf_Util_SessionToken::getSessionToken()) {
            if (!$this->_getParam('kwfSessionToken')) {
                throw new Kwf_Exception("Missing sessionToken parameter");
            }
            if ($this->_getParam('kwfSessionToken') != Kwf_Util_SessionToken::getSessionToken()) {
                throw new Kwf_Exception("Invalid kwfSessionToken");
            }
        }

        $allowed = false;
        if ($this->_getUserRole() == 'cli') {
            $allowed = true;
        } else {
            $acl = Zend_Registry::get('acl');
            $resource = $this->getRequest()->getResourceName();
            if (!$acl->has($resource)) {
                throw new Kwf_Exception_NotFound();
            } else {
                if ($this->_getAuthData()) {
                    $allowed = $acl->isAllowedUser($this->_getAuthData(), $resource, 'view');
                } else {
                    $allowed = $acl->isAllowed($this->_getUserRole(), $resource, 'view');
                }
            }
        }

        if (!$allowed) {
            $params = array(
                'resource' => $resource,
                'role' => $this->_getUserRole()
            );
            $this->_forward('json-login', 'login',
                                'kwf_controller_action_user', $params);
        }
    }

    public function postDispatch()
    {
        Kwf_Component_ModelObserver::getInstance()->process();
        Kwf_Component_Cache::getInstance()->writeBuffer();
    }

    protected function _getUserRole()
    {
        if (php_sapi_name() == 'cli') return 'cli';
        return Kwf_Registry::get('userModel')->getAuthedUserRole();
    }

    protected function _getAuthData()
    {
        if (php_sapi_name() == 'cli') return null;
        return Kwf_Registry::get('userModel')->getAuthedUser();
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
            $ors = array();
            if (!$this->_queryColumns) {
                throw new Kwf_Exception("_queryColumns are required");
            }
            foreach ($this->_queryColumns as $c) {
                $ors[] = new Kwf_Model_Select_Expr_Like($c, '%'.$filter->value.'%');
            }
            $select->where(new Kwf_Model_Select_Expr_Or($ors));
        } else {
            if (!is_null($filter->value)) {
                $select->whereEquals($filter->property, $filter->value);
            }
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

    // Handle GET and return a list of resources
    public function indexAction()
    {
        $this->view->data = array();
        $s = $this->_getSelectIndex();
        foreach ($this->_model->getRows($s) as $row) {
            $this->view->data[] = $this->_loadDataFromRow($row);
        }
        $this->view->total = $this->_model->countRows($s);
    }

    // Handle GET and return a specific resource item
    public function getAction()
    {
        $row = $this->_model->getRow($this->_getParam('id'));
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
        $row->save();

        $this->view->data = $this->_loadDataFromRow($row);
    }

    protected function _fillRowInsert($row, $data)
    {
        $this->_fillRow($row, $data);
    }

    private function _fillRow($row, $data)
    {
        foreach ($this->_saveColumns as $col) {
            if (!isset($data->$col)) continue;
            $v = $data->$col;
            if ($v === null) continue;
            if ($this->_model->getColumnType($col) == Kwf_Model_Interface::TYPE_DATE) {
                $v = new Kwf_Date($v);
                $v = $v->format();
            } else if ($this->_model->getColumnType($col) == Kwf_Model_Interface::TYPE_DATETIME) {
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

        $row = $this->_model->getRow($this->_getParam('id'));
        if (!$row) throw new Kwf_Exception_NotFound();

        $this->_fillRow($row, $data);
        $this->_beforeUpdate($row);
        $row->save();

        $this->view->data = $this->_loadDataFromRow($row);
    }

    // Handle DELETE requests to delete a specific item
    public function deleteAction()
    {
        $row = $this->_model->getRow($this->_getParam('id'));
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

    protected function _beforeDelete(Kwf_Model_Row_Interface $row)
    {
    }
}
