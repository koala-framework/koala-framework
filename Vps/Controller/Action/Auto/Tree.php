<?p
abstract class Vps_Controller_Action_Auto_Tree extends Vps_Controller_Acti

    const ADD_LAST = 0
    const ADD_FIRST = 1
    protected $_tableNam
    protected $_tabl
    protected $_icons = array
        'root'      => 'folder
        'default'   => 'table
        'edit'      => 'table_edit
        'invisible' => 'table_key
        'add'       => 'table_add
        'delete'    => 'table_delet
    
    protected $_textField = 'text
    protected $_buttons = arra
        'add'       => tru
        'edit'      => fals
        'delete'    => tru
        'invisible' => nul
        'reload'    => tru
        'expandAll' => tru
        'collapseAll'=> tr
    
    protected $_rootText = 'Root
    protected $_rootVisible = tru
    protected $_hasPosition; // Gibt es ein pos-Fe
    protected $_editDialo
    private $_openedNodes = array()
    protected $_addPosition = self::ADD_FIRST

    public function init
   
        if (!isset($this->_table))
            $this->_table = new $this->_tableName(
       

        $info = $this->_table->info(

        // Invisible-Button hinzufügen falls nicht überschrieben und in 
        if (array_key_exists('invisible', $this->_buttons) 
            is_null($this->_buttons['invisible']) 
            in_array('visible', $info['cols']
       
            $this->_buttons['invisible'] = tru
        
       
        // Pos-Fe
        if (!isset($this->_hasPosition)) 
            $this->_hasPosition = in_array('pos', $info['cols'])
        
        if ($this->_hasPosition && !in_array('pos', $info['cols'])) 
            throw new Vps_Exception("_hasPosition is true, but 'pos' does not exist in database")
        
   

    protected function jsonMetaAction
   
        $this->view->icons = $this->_icon
        $this->view->enableDD = $this->_hasPositio
        $this->view->rootText = $this->_rootTex
        $this->view->rootVisible = $this->_rootVisibl
        $this->view->buttons = $this->_button
        $this->view->editDialog = $this->_editDialo
   

    public function jsonDataAction
   
        $parentId = $this->_getParam('node'

        $this->_saveSessionNodeOpened($parentId, true
        $this->_saveNodeOpened(

        $order = $this->_hasPosition ? 'pos' : null 
        $rowset = $this->_table->fetchAll($this->_getWhere(), $order

        $nodes = array(
        foreach ($rowset as $row)
            $nodes[] = $this->_formatNode($row
       
        $this->view->nodes = $node
   

    public function jsonNodeDataAction
   
        $id = $this->getRequest()->getParam('node'
        $row = $this->_table->find($id)->current(
        if ($row)
            $this->view->data = $this->_formatNode($row
        } else
            throw new Vps_ClientException('Couldn\'t find row with id ' . $id
       
   

    protected function _getWhere
   
        $where = array(
        $parentId = $this->getRequest()->getParam('node'
        if (!$parentId)
            $where['parent_id IS NULL'] = '
        } else
            $where['parent_id = ?'] = $parentI
       
        return $wher
   

    protected function _formatNode($ro
   
        $data = array(
        $data['id'] = $row->i
        $data['text'] = $row->nam
        $data['data'] = $row->toArray(
        $data['leaf'] = fals
        $data['visible'] = tru
        $data['bIcon'] = $this->_icons['default'
        if ($row->visible == '0')
            $data['visible'] = fals
            $data['bIcon'] = $this->_icons['invisible'
       
        $openedNodes = $this->_saveSessionNodeOpened(null, null
        if ($this->_table->fetchAll('parent_id = ' . $row->id)->count() > 0)
            if (isset($openedNodes[$row->id]) 
                isset($this->_openedNodes[$row->id
            )
                $data['expanded'] = tru
            } else
                $data['expanded'] = fals
           
        } else
            $data['children'] = array(
            $data['expanded'] = tru
       
        $data['uiProvider'] = 'Vps.Auto.TreeNode
        return $dat
   

    protected function _saveSessionNodeOpened($id, $activat
   
        $session = new Zend_Session_Namespace('admin'
        $key = 'treeNodes_' . get_class($this->_table
        $ids = is_array($session->$key) ? $session->$key : array(
        if ($id)
            if (!$activate && isset($ids[$id]))
                unset($ids[$id]
            } else if ($activate)
                $ids[$id] = tru
           
            $session->$key = $id
       
        return $id
   

    protected function _saveNodeOpened
   
        $openedId = $this->_getParam('openedId'
        $this->_openedNodes = array(
        while ($openedId)
            $row = $this->_table->find($openedId)->current(
            $this->_openedNodes[$openedId] = tru
            $openedId = $row ? $row->parent_id : nul
       
   

    public function jsonVisibleAction
   
        $visible = $this->getRequest()->getParam('visible') == 'true
        $id = $this->getRequest()->getParam('id'
        $row = $this->_table->find($id)->current(
        $row->visible = $row->visible == '0' ? '1' : '0
        $this->view->id = $row->save(
        $this->view->visible = $row->visible == '1
   

    public function jsonAddAction
   
        $insert['parent_id'] = $this->getRequest()->getParam('parentId'
        $insert[$this->_textField] = $this->getRequest()->getParam('name')
        if ($this->_hasPosition) 
            $insert['pos'] = 0
       
        $id = $this->_table->insert($insert)
        $row = $this->_table->find($id)->current()
        if ($this->_hasPosition) 
            $where = $this->_getWhere()
            $row->numberize('pos', $this->_addPosition, $where)
       
        if ($id)
            $this->view->data = $this->_formatNode($row
        } else
            $this->view->error = 'Couldn\'t insert row.
       
   

    public function jsonDeleteAction
   
        $id = $this->getRequest()->getParam('id'
        $row = $this->_table->find($id)->current(
        if (!$row) throw new Vps_Exception("No entry with id '$id' found"
        if ($row)
            $row->delete(
            $this->view->id = $i
       
   

    public function jsonMoveAction
   
        $source = $this->getRequest()->getParam('source'
        $target = $this->getRequest()->getParam('target'
        $point  = $this->getRequest()->getParam('point'

        $row = $this->_table->find($source)->current(
        if ($point == 'append')
            $row->parent_id = (int)$target == 0 ? null : $targe
            if ($this->_hasPosition)
                $row->pos = '1
           
        } else
            $targetRow = $this->_table->find($target)->current()
            if ($targetRow)
                $row->parent_id = $targetRow->parent_i
                if ($this->_hasPosition)
                    $targetPosition = $targetRow->po
                    if ($point == 'above')
                        $row->pos = $targetPosition - 
                    } else
                        $row->pos = $targetPositio
                   
               
            } else
                $this->view->error = 'Cannot move here.
           
       

        $row->save(
        if ($this->_hasPosition)
            if (!$row->parent_id)
                $where = array('parent_id IS NULL' => ''
            } else
                $where = array('parent_id = ?' => $row->parent_id
           
            $row->numberize('pos', $row->pos, $where
       
   

    public function jsonCollapseAction
   
        $id = $this->getRequest()->getParam('id'
        $this->_saveSessionNodeOpened($id, false
   

    public function jsonExpandAction
   
        $id = $this->getRequest()->getParam('id'
        $this->_saveSessionNodeOpened($id, true
   

