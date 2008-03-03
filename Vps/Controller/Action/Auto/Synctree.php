<?php
abstract class Vps_Controller_Action_Auto_Synctree extends Vps_Controller_Action
{
    const ADD_LAST = 0;
    const ADD_FIRST = 1;
    protected $_tableName;
    protected $_table;
    protected $_icons = array (
        'root'      => 'folder',
        'default'   => 'table',
        'edit'      => 'table_edit',
        'invisible' => 'table_key',
        'add'       => 'table_add',
        'delete'    => 'table_delete'
    );
    protected $_textField = 'text';
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

    public function preDispatch()
    {
        parent::preDispatch();

        if (!isset($this->_table)) {
            $this->_table = new $this->_tableName();
        }

        $info = $this->_table->info();

        // Invisible-Button hinzufügen falls nicht überschrieben und in DB
        if (array_key_exists('invisible', $this->_buttons) &&
            is_null($this->_buttons['invisible']) &&
            in_array('visible', $info['cols'])) {

            $this->_buttons['invisible'] = true;
        }

        // Pos-Feld
        if (!isset($this->_hasPosition)) {
            $this->_hasPosition = in_array('pos', $info['cols']);
        }
        if ($this->_hasPosition && !in_array('pos', $info['cols'])) {
            throw new Vps_Exception("_hasPosition is true, but 'pos' does not exist in database");
        }

        foreach ($this->_icons as $k=>$i) {
            if (is_string($i)) {
                $this->_icons[$k] = new Vps_Asset($i);
            }
        }
    }

    protected function jsonMetaAction()
    {
        $this->view->icons = array();
        foreach ($this->_icons as $k=>$i) {
            $this->view->icons[$k] = $i->__toString();
        }
        $this->view->enableDD = $this->_hasPosition;
        $this->view->rootText = $this->_rootText;
        $this->view->rootVisible = $this->_rootVisible;
        $this->view->buttons = $this->_buttons;
        $this->view->editDialog = $this->_editDialog;
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
        $row = $this->_table->find($id)->current();
        if ($row) {
            $this->view->data = $this->_formatNode($row);
        } else {
            throw new Vps_ClientException('Couldn\'t find row with id ' . $id);
        }
    }

    protected function _getTreeWhere($parentId = null)
    {
        $where = $this->_getWhere();
        if (!$parentId) {
            $where['parent_id IS NULL'] = '';
        } else {
            $where['parent_id = ?'] = $parentId;
        }
        return $where;
    }

    protected function _getWhere()
    {
        return array();
    }

    protected function _formatNodes($parentId = null)
    {
        $nodes = array();
        $order = $this->_hasPosition ? 'pos' : null ;
        $rows = $this->_table->fetchAll($this->_getTreeWhere($parentId), $order);
        foreach ($rows as $row) {
            $data = array();
            $data['id'] = $row->id;
            $data['text'] = $row->name;
            $data['data'] = $row->toArray();
            $data['leaf'] = false;
            $data['uiProvider'] = 'Vps.Auto.TreeNode';
            if ($row->visible == '0') {
                $data['visible'] = false;
                $data['bIcon'] = $this->_icons['invisible']->__toString();
            } else {
                $data['visible'] = true;
                $data['bIcon'] = $this->_icons['default']->__toString();
            }

            $openedNodes = $this->_saveSessionNodeOpened(null, null);
            if ($openedNodes == 'all' ||
                isset($openedNodes[$row->id]) ||
                isset($this->_openedNodes[$row->id])
            ) {
                $data['expanded'] = true;
            } else {
                $data['expanded'] = false;
            }

            $data['children'] = $this->_formatNodes((int)$row->id);
            if (sizeof($data['children']) == 0) {
                $data['expanded'] = true;
            }
            $nodes[] = $data;
        }
        return $nodes;
    }

    protected function _formatNode($row)
    {
        $data = array();
        $data['id'] = $row->id;
        $data['text'] = $row->name;
        $data['data'] = $row->toArray();
        $data['leaf'] = false;
        $data['visible'] = true;
        $data['bIcon'] = $this->_icons['default']->__toString();
        if ($row->visible == '0') {
            $data['visible'] = false;
            $data['bIcon'] = $this->_icons['invisible']->__toString();
        }
        $openedNodes = $this->_saveSessionNodeOpened(null, null);
        if ($this->_table->fetchAll('parent_id = ' . $row->id)->count() > 0) {
            if (isset($openedNodes[$row->id]) ||
                isset($this->_openedNodes[$row->id])
            ) {
                $data['expanded'] = true;
            } else {
                $data['expanded'] = false;
            }
        } else {
            $data['children'] = array();
            $data['expanded'] = true;
        }
        $data['uiProvider'] = 'Vps.Auto.TreeNode';
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
            $row = $this->_table->find($openedId)->current();
            $this->_openedNodes[$openedId] = true;
            $openedId = $row ? $row->parent_id : null;
        }
    }

    public function jsonVisibleAction()
    {
        $visible = $this->getRequest()->getParam('visible') == 'true';
        $id = $this->getRequest()->getParam('id');
        $row = $this->_table->find($id)->current();
        $row->visible = $row->visible == '0' ? '1' : '0';
        $this->view->id = $row->save();
        $this->view->visible = $row->visible == '1';
    }

    public function jsonAddAction()
    {
        $insert['parent_id'] = $this->getRequest()->getParam('parentId');
        $insert[$this->_textField] = $this->getRequest()->getParam('name');
        if ($this->_hasPosition) {
            $insert['pos'] = 0;
        }
        $id = $this->_table->insert($insert);
        $row = $this->_table->find($id)->current();
        if ($this->_hasPosition) {
            $where = $this->_getTreeWhere($insert['parent_id']);
            $row->numberize('pos', $this->_addPosition, $where);
        }
        if ($id) {
            $data = $this->_formatNode($row);
            foreach ($data as $k=>$i) {
                if ($i instanceof Vps_Asset) {
                    $data[$k] = $i->__toString();
                }
            }
            $this->view->data = $data;
        } else {
            $this->view->error = 'Couldn\'t insert row.';
        }
    }

    public function jsonDeleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $row = $this->_table->find($id)->current();
        if (!$row) throw new Vps_Exception("No entry with id '$id' found");
        if ($row) {
            $row->delete();
            $this->view->id = $id;
        }
    }

    public function jsonMoveAction()
    {
        $source = $this->getRequest()->getParam('source');
        $target = $this->getRequest()->getParam('target');
        $point  = $this->getRequest()->getParam('point');

        $row = $this->_table->find($source)->current();
        if ($point == 'append') {
            $row->parent_id = (int)$target == 0 ? null : $target;
            if ($this->_hasPosition) {
                $row->pos = '1';
            }
        } else {
            $targetRow = $this->_table->find($target)->current();
            if ($targetRow) {
                $row->parent_id = $targetRow->parent_id;
                if ($this->_hasPosition) {
                    $targetPosition = $targetRow->pos;
                    if ($point == 'above') {
                        $row->pos = $targetPosition;
                    } else {
                        $row->pos = $targetPosition + 1;
                    }
                }
            } else {
                $this->view->error = 'Cannot move here.';
            }
        }

        $row->save();
        if ($this->_hasPosition) {
            if (!$row->parent_id) {
                $where = array('parent_id IS NULL' => '');
            } else {
                $where = array('parent_id = ?' => $row->parent_id);
            }
            $row->numberize('pos', $row->pos, $where);
        }
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
