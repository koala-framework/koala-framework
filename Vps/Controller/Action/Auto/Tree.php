<?php
abstract class Vps_Controller_Action_Auto_Tree extends Vps_Controller_Action_Auto_Synctree
{
    public function indexAction()
    {
        $config = array(
            'controllerUrl' => $this->getRequest()->getPathInfo()
        );
        $this->view->ext('Vps.Auto.TreePanel', $config);
    }
    
    protected function _getTreeWhere($parentId = null)
    {
        $where = $this->_getWhere();
        if ($this->_model instanceof Vps_Model_Db) {
            if (!$parentId) {
                $where[] = "$this->_parentField IS NULL";
            } else {
                $where["$this->_parentField = ?"] = $parentId;
            }
        } else {
            $where['parent'] = $parentId;
        }
        return $where;
    }
    
    public function jsonDataAction()
    {
        $parentId = $this->_getParam('node');

        $this->_saveSessionNodeOpened($parentId, true);
        $this->_saveNodeOpened();

        $order = $this->_hasPosition ? 'pos' : null ;
        $where = $this->_getTreeWhere($parentId);
        $rowset = $this->_model->fetchAll($where, $order);

        $nodes = array();
        foreach ($rowset as $row) {
            $data = $this->_formatNode($row);
            foreach ($data as $k=>$i) {
                if ($i instanceof Vps_Asset) {
                    $data[$k] = $i->__toString();
                }
            }
            $nodes[]= $data;
        }
        $this->view->nodes = $nodes;
    }
    
    protected function _formatNodes($parentId = null)
    {
        return array();
    }
    
    protected function _formatNode($row)
    {
        $data = parent::_formatNode($row);
        unset($data['children']);
        if ($this->_model->fetchAll($this->_getTreeWhere($row->{$this->_primaryKey}))->count() > 0) {
            $openedNodes = $this->_saveSessionNodeOpened(null, null);
            if ($openedNodes == 'all' ||
                isset($openedNodes[$row->{$this->_primaryKey}]) ||
                isset($this->_openedNodes[$row->{$this->_primaryKey}])
            ) {
                $data['expanded'] = true;
            } else {
                $data['expanded'] = false;
            }
        } else {
            $data['children'] = array();
            $data['expanded'] = true;
            $data['leaf'] = true;
        }
        return $data;
    }
}
