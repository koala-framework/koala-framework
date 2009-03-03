<?php
abstract class Vps_Controller_Action_Auto_Synctree extends Vps_Controller_Action_Auto_Abstract
{
    const ADD_LAST = 0;
    const ADD_FIRST = 1;

    protected $_primaryKey;
    protected $_table;
    protected $_tableName;
    protected $_model;
    protected $_modelName;

    protected $_icons = array (
        'root'      => 'folder',
        'default'   => 'table',
        'edit'      => 'table_edit',
        'invisible' => 'table_key',
        'add'       => 'table_add',
        'delete'    => 'table_delete'
    );
    protected $_textField = 'name';
    protected $_parentField = 'parent_id';
    protected $_buttons = array(
        'add'       => true,
        'edit'      => false,
        'delete'    => true,
        'invisible' => null,
        'reload'    => true
    );
    protected $_rootText = 'Root';
    protected $_rootVisible = true;
    protected $_hasPosition; // Gibt es ein pos-Feld
    protected $_editDialog;
    private $_openedNodes = array();
    protected $_addPosition = self::ADD_FIRST;
    protected $_enableDD;
    protected $_defaultOrder;

    public function indexAction()
    {
        $config = array(
            'controllerUrl' => $this->getRequest()->getPathInfo()
        );
        $this->view->ext('Vps.Auto.SyncTreePanel', $config);
    }

    public function setTable($table)
    {
        $this->_model = new Vps_Model_Db(array(
            'table' => $table
        ));
    }

    private function _getTableInfo()
    {
        if (!isset($this->_model) || !($this->_model instanceof Vps_Model_Db)) {
            return null;
        }
        return $this->_model->getTable()->info();
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if (isset($this->_modelName)) {
            $modelName = $this->_modelName;
            $this->_model = new $modelName();
        } else if (!$this->_model) {
            if (!isset($this->_table)) {
                $this->_table = new $this->_tableName();
            }
            $this->_model = new Vps_Model_Db(array(
                'table' => $this->_table
            ));
        }

        // PrimaryKey setzen
        if (!isset($this->_primaryKey)) {
            $this->_primaryKey = $this->_model->getPrimaryKey();
            if (is_array($this->_primaryKey)) {
                $this->_primaryKey = $this->_primaryKey[1];
            }
        }

        $cols = $this->_model->getColumns();
        // Invisible-Button hinzufügen falls nicht überschrieben und in DB
        if (array_key_exists('invisible', $this->_buttons) &&
            is_null($this->_buttons['invisible']) &&
            in_array('visible', $cols))
        {
            $this->_buttons['invisible'] = true;
        }

        // Pos-Feld
        if (!isset($this->_hasPosition)) {
            $this->_hasPosition = in_array('pos', $cols);
        }
        if ($this->_hasPosition && !in_array('pos', $cols)) {
            throw new Vps_Exception("_hasPosition is true, but 'pos' does not exist in database");
        }

        foreach ($this->_icons as $k=>$i) {
            if (is_string($i)) {
                $this->_icons[$k] = new Vps_Asset($i);
            }
        }

        if (is_string($this->_defaultOrder)) {
            $this->_defaultOrder = array(
                'field' => $this->_defaultOrder,
                'direction'   => 'ASC'
            );
        }
    }

    public function jsonMetaAction()
    {
        $this->view->helpText = $this->getHelpText();
        $this->view->icons = array();
        foreach ($this->_icons as $k=>$i) {
            $this->view->icons[$k] = $i->__toString();
        }
        $this->view->rootText = $this->_rootText;
        $this->view->rootVisible = $this->_rootVisible;
        $this->view->buttons = $this->_buttons;
        $this->view->editDialog = $this->_editDialog;
        if (is_null($this->_enableDD)) {
            $this->view->enableDD = $this->_hasPosition;
        } else {
            $this->view->enableDD = $this->_enableDD;
            if ($this->_enableDD) {
                $this->view->dropConfig = array('appendOnly' => true);
            }
        }
    }

    public function jsonDataAction()
    {
        $parentId = $this->_getParam('node');

        $this->_saveSessionNodeOpened($parentId, true);
        $this->_saveNodeOpened();

        $this->view->nodes = $this->_formatNodes();
    }

    public function jsonNodeDataAction()
    {
        $id = $this->getRequest()->getParam('node');
        $row = $this->_model->find($id)->current();
        if ($row) {
            $this->view->data = $this->_formatNode($row);
        } else {
            throw new Vps_ClientException('Couldn\'t find row with id ' . $id);
        }
    }

    protected function _getTreeWhere($parentRow = null)
    {
        return $this->_getWhere();
    }

    protected function _getWhere()
    {
        return array();
    }

    protected function _formatNodes($parentRow = null)
    {
        $nodes = array();
        $select = $this->_getSelect($this->_getTreeWhere($parentRow));
        if (!$parentRow) {
            $select->whereNull($this->_parentField);
        } else {
            $select->whereEquals($this->_parentField, $parentRow->{$this->_primaryKey});
        }
        $rows = $this->_model->fetchAll($select);
        foreach ($rows as $row) {
            $nodes[] = $this->_formatNode($row);
        }
        return $nodes;
    }

    protected function _getSelect($where)
    {
        $select = $this->_model->select($where);
        if ($this->_hasPosition) {
            $select->order('pos');
        } else if (!$select->hasPart('order') && $this->_defaultOrder) {
            $select->order(
                $this->_defaultOrder['field'],
                $this->_defaultOrder['direction']
            );
        }
        return $select;
    }

    protected function _formatNode($row)
    {
        $data = array();
        $primaryKey = $this->_primaryKey;
        $data['id'] = $row->$primaryKey;
        $data['text'] = $row->{$this->_textField};
        $data['data'] = $row->toArray();
        $data['leaf'] = false;
        $data['visible'] = true;
        $data['uiProvider'] = 'Vps.Tree.Node';
        if ($row->visible == '0') {
            $data['visible'] = false;
            $data['bIcon'] = $this->_icons['invisible']->__toString();
        } else {
            $data['visible'] = true;
            $data['bIcon'] = $this->_icons['default']->__toString();
        }
        $openedNodes = $this->_saveSessionNodeOpened(null, null);
        $data['uiProvider'] = 'Vps.Tree.Node';
        if ($openedNodes == 'all' ||
            isset($openedNodes[$row->$primaryKey]) ||
            isset($this->_openedNodes[$row->id])
        ) {
            $data['expanded'] = true;
        } else {
            $data['expanded'] = false;
        }
        $data['children'] = $this->_formatNodes($row);
        if (sizeof($data['children']) == 0) {
            $data['expanded'] = true;
        }
        $nodes[] = $data;
        return $data;
    }

    protected function _saveSessionNodeOpened($id, $activate)
    {
        $session = new Zend_Session_Namespace('admin');
        $key = 'treeNodes_' . get_class($this);
        $ids = is_array($session->$key) ? $session->$key : array();
        if ($id) {
            if (!$activate && isset($ids[$id])) {
                unset($ids[$id]);
            } else if ($activate) {
                $ids[$id] = true;
            }
            $session->$key = $ids;
        }
        return $ids;
    }

    protected function _saveNodeOpened()
    {
        $openedId = $this->_getParam('openedId');
        $this->_openedNodes = array();
        while ($openedId) {
            $row = $this->_model->find($openedId)->current();
            $this->_openedNodes[$openedId] = true;
            $field = $this->_parentField;
            $openedId = $row ? $row->$field : null;
        }
    }

    public function jsonVisibleAction()
    {
        $visible = $this->getRequest()->getParam('visible') == 'true';
        $id = $this->getRequest()->getParam('id');
        $row = $this->_model->find($id)->current();
        $row->visible = $row->visible == '0' ? '1' : '0';
        $this->view->id = $row->save();
        $this->view->visible = $row->visible == '1';
    }

    public function jsonAddAction()
    {
        $insert[$this->_parentField] = $this->getRequest()->getParam('parentId');
        $insert[$this->_textField] = $this->getRequest()->getParam('name');
        if ($this->_hasPosition) {
            $insert['pos'] = 0;
        }
        $row = $this->_model->createRow($insert);
        $row->save();
        $data = $this->_formatNode($row);
        foreach ($data as $k=>$i) {
            if ($i instanceof Vps_Asset) {
                $data[$k] = $i->__toString();
            }
        }
        $this->view->data = $data;
    }

    public function jsonDeleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $row = $this->_model->find($id)->current();
        if (!$row) throw new Vps_Exception("No entry with id '$id' found");
        $row->delete();
        $this->view->id = $id;
    }

    public function jsonMoveAction()
    {
        $source = $this->getRequest()->getParam('source');
        $target = $this->getRequest()->getParam('target');
        $point  = $this->getRequest()->getParam('point');

        $parentField = $this->_parentField;
        $row = $this->_model->getTable()->find($source)->current();

        if ($point == 'append') {
            $row->$parentField = (int)$target == 0 ? null : $target;
            if ($this->_hasPosition) {
                $row->pos = '1';
            }
        } else {
            $targetRow = $this->_model->getTable()->find($target)->current();
            if ($targetRow) {
                if ($this->_hasPosition) {
                    $targetPosition = $targetRow->pos;
                    if ($point == 'below') {
                        $targetPosition++;
                    }
                    if ($row->$parentField == $targetRow->$parentField &&
                        $row->pos < $targetRow->pos
                    ) {
                         $targetPosition--;
                    }
                    $row->pos = $targetPosition;
                }
                $row->$parentField = $targetRow->$parentField;
            } else {
                $this->view->error = 'Cannot move here.';
            }
        }
        $row->save();

        $row = $this->_model->find($row->id)->current();
        $primaryKey = $this->_model->getPrimaryKey();
        $before = null;
        $select = $this->_getSelect($this->_getTreeWhere());
        $parentValue = $row->$parentField;
        if (!$parentValue) {
            $select->whereNull($this->_parentField);
        } else {
            $select->whereEquals($this->_parentField, $parentValue);
        }
        foreach ($this->_model->fetchAll($select) as $r) {
            if ($before === true) $before = $r->$primaryKey;
            if ($r->$primaryKey == $source) {
                $before = true;
            }
        }
        if ($before === true) $before = null;

        $this->view->parent = $row->$parentField;
        $this->view->node = $source;
        $this->view->before = $before;
    }

    public function jsonCollapseAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->_saveSessionNodeOpened($id, false);
    }

    public function jsonExpandAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->_saveSessionNodeOpened($id, true);
    }
}
