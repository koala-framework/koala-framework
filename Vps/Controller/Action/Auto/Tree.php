<?php
abstract class Vps_Controller_Action_Auto_Tree extends Vps_Controller_Action
{
    protected $_treeTableName;
    protected $_treeTable;
    protected $_treeIcons = array (
        'root' => 'folder',
        'default' => 'table',
        'edit' => 'table_edit',
        'invisible' => 'table_key',
        'add' => 'table_add',
        'delete' => 'table_delete'
    );
    protected $_treeTextField = 'text';
    protected $_treeButtons = array(
        'add' => true,
        'edit' => false,
        'delete' => true,
        'invisible' => null,
        'reload' => true,
        'expand' => true,
        'collapse' => true
    );
    protected $_rootText = 'Root';
    protected $_rootVisible = true;
    protected $_treeOrder = null;
    protected $_hasPosition = null;
    
    public function init()
    {
        if (!isset($this->_treeTable)) {
            $this->_treeTable = new $this->_treeTableName();
        }
        
        $info = $this->_treeTable->info();

        // Invisible-Button hinzufügen falls nicht überschrieben und in DB
        if (array_key_exists('invisible', $this->_treeButtons) && 
            is_null($this->_treeButtons['invisible']) && 
            in_array('visible', $info['cols']))
        {
            $this->_treeButtons['invisible'] = true;
        }

        // Sortierung aktivieren wenn in DB
        if (!$this->_hasPosition) {
            $this->_hasPosition = in_array('position', $info['cols']);
            $this->_treeOrder = 'position';
        }
    }
    
    protected function jsonMetaAction()
    {
        $this->view->icons = $this->_treeIcons;
        $this->view->enableDD = $this->_hasPosition;
        $this->view->rootText = $this->_rootText;
        $this->view->rootVisible = $this->_rootVisible;
        $this->view->buttons = $this->_treeButtons;
    }

    public function jsonDataAction()
    {
        $parentId = $this->getRequest()->getParam('node');
        $this->_saveSessionNodeOpened($parentId, true);
        
        $rowset = $this->_treeTable->fetchAll($this->_getWhere(), $this->_treeOrder);
        
        $nodes = array();
        foreach ($rowset as $row) {
            $nodes[] = $this->_formatNode($row);
        }
        $this->view->nodes = $nodes;
    }
    
    protected function _getWhere()
    {
        $parentId = $this->getRequest()->getParam('node');
        $where[] = $this->_treeTable->getAdapter()->quoteInto('parent_id = ?', $parentId);
        return $where;
    }
    
    protected function _formatNode($row)
    {
        $openedNodes = $this->_saveSessionNodeOpened(null, null);

        $data = array();
        $data['id'] = $row->id;
        $data['text'] = $row->name;
        $data['leaf'] = false;
        $data['visible'] = true;
        $data['bIcon'] = $this->_treeIcons['default'];
        if ($this->_treeButtons['invisible'] && $row->visible == '0') {
            $data['visible'] = false;
            $data['bIcon'] = $this->_treeIcons['invisible'];
        }
        if ($this->_treeTable->fetchAll('parent_id = ' . $row->id)->count() > 0) {
            if (isset($openedNodes[$row->id])) {
                $data['expanded'] = true;
            } else {
                $data['expanded'] = false;
            }
        } else {
            $data['children'] = array();
            $data['expanded'] = true;
        }
        $data['uiProvider'] = 'Vps.AutoTree.Node';
        return $data;
    }
    
    private function _saveSessionNodeOpened($id, $activate)
    {
        $session = new Zend_Session_Namespace('admin');
        $key = 'treeNodes_' . get_class($this->_treeTable);
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

    public function jsonVisibleAction()
    {
        $visible = $this->getRequest()->getParam('visible') == 'true';
        $id = $this->getRequest()->getParam('id');
        $row = $this->_treeTable->find($id)->current();
        $row->visible = $visible ? '1' : '0';
        $this->view->id = $row->save();
        $this->view->visible = $row->visible == '1';
    }

    public function jsonAddAction()
    {
        $insert['parent_id'] = $this->getRequest()->getParam('parentId');
        $insert[$this->_treeTextField] = $this->getRequest()->getParam('name');
        $id = $this->_treeTable->insert($insert);
        if ($id) {
            $this->view->parentId = $insert['parent_id'];
            $this->view->config = $this->_formatNode($this->_treeTable->find($id)->current());
        } else {
            $this->view->error = 'Couldn\'t insert row.'; 
        }
    }

    public function jsonDeleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $where = $this->_treeTable->getAdapter()->quoteInto('id = ?', $id);
        if ($this->_treeTable->delete($where) > 0) {
            $this->view->id = $id;
        } else {
            $this->view->error = 'Kein Eintrag gelöscht.';
        }
    }

    public function jsonMoveAction()
    {
        $source = $this->getRequest()->getParam('source');
        $target = $this->getRequest()->getParam('target');
        $point  = $this->getRequest()->getParam('point');

        if ($point == 'append') {
            $parentId = $target;
            $position = '1';
        } else {
            $targetRow = $this->_treeTable->find($target)->current();
            $parentId = $targetRow->parent_id;
            if ($this->_hasPosition) {
                $targetPosition = $targetRow->position;
                if ($point == 'above') {
                    $position = $targetPosition - 1;
                } else {
                    $position = $targetPosition;
                }
            }
        }
        $row = $this->_treeTable->find($source)->current();
        $row->parent_id = $parentId;
        $row->save();
        if ($this->_hasPosition) {
            $row->numberize('position', $position, 'parent_id = ' . $parentId);
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
