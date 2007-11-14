<?php
abstract class Vps_Controller_Action_Auto_Tree extends Vps_Controller_Action
{
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
        'reload'    => true,
        'expandAll' => true,
        'collapseAll'=> true
    );
    protected $_rootText = 'Root';
    protected $_rootVisible = true;
    protected $_order = null;
    protected $_enableDD;
    protected $_hasPosition;
    protected $_editDialog;
    private $_openedNodes = array();

    public function init()
    {
        if (!isset($this->_table)) {
            $this->_table = new $this->_tableName();
        }

        $info = $this->_table->info();

        // Invisible-Button hinzufügen falls nicht überschrieben und in DB
        if (array_key_exists('invisible', $this->_buttons) &&
            is_null($this->_buttons['invisible']) &&
            in_array('visible', $info['cols']))
        {
            $this->_buttons['invisible'] = true;
        }

        // Sortierung aktivieren wenn in DB
        if (!isset($this->_hasPosition)) {
            $this->_hasPosition = in_array('pos', $info['cols']);
        }
        if ($this->_hasPosition && isset($this->_order) && $this->_order != 'pos') {
            throw new Vps_Exception("If _hasposition is enabled, order must be 'pos'");
        }
        if ($this->_hasPosition) {
            $this->_order = 'pos';
        }

        // Drag&Drop standardmäßig aktivieren wenn _hasPosition aktiviert ist
        if (!isset($this->_enableDD)) {
            $this->_enableDD = $this->_hasPosition;
        }
    }

    protected function jsonMetaAction()
    {
        $this->view->icons = $this->_icons;
        $this->view->enableDD = $this->_enableDD;
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

        $rowset = $this->_table->fetchAll($this->_getWhere(), $this->_order);

        $nodes = array();
        foreach ($rowset as $row) {
            $nodes[] = $this->_formatNode($row);
        }
        $this->view->nodes = $nodes;
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

    protected function _getWhere()
    {
        $where = array();
        $parentId = $this->getRequest()->getParam('node');
        if (!$parentId) {
            $where[] = 'parent_id IS NULL';
        } else {
            $where[] = $this->_table->getAdapter()->quoteInto('parent_id = ?', $parentId);
        }
        return $where;
    }

    protected function _formatNode($row)
    {
        $data = array();
        $data['id'] = $row->id;
        $data['text'] = $row->name;
        $data['data'] = $row->toArray();
        $data['leaf'] = false;
        $data['visible'] = true;
        $data['bIcon'] = $this->_icons['default'];
        if ($row->visible == '0') {
            $data['visible'] = false;
            $data['bIcon'] = $this->_icons['invisible'];
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
        $key = 'treeNodes_' . get_class($this->_table);
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
        if (!$insert['parent_id']) $insert['parent_id'] = null;
        $insert[$this->_textField] = $this->getRequest()->getParam('name');
        $id = $this->_table->insert($insert);
        if ($id) {
            $this->view->data = $this->_formatNode($this->_table->find($id)->current());
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
                        $row->pos = $targetPosition - 1;
                    } else {
                        $row->pos = $targetPosition;
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
