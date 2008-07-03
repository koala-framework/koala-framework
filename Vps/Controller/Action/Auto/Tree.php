<?php
abstract class Vps_Controller_Action_Auto_Tree extends Vps_Controller_Action_Auto_Synctree
{
    public function jsonDataAction()
    {
        $parentId = $this->_getParam('node');

        $this->_saveSessionNodeOpened($parentId, true);
        $this->_saveNodeOpened();

        $order = $this->_hasPosition ? 'pos' : null ;
        $where = $this->_getTreeWhere($parentId);
        $rowset = $this->_table->fetchAll($where, $order);

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
        return parent::_formatNode($row);
        $where = array("$this->_parentField = ?" => $row->$primaryKey);
        if ($this->_table->fetchAll($where)->count() > 0) {
            if (isset($openedNodes[$row->$primaryKey]) ||
                isset($this->_openedNodes[$row->$primaryKey])
            ) {
                $data['expanded'] = true;
            } else {
                $data['expanded'] = false;
            }
        } else {
            $data['children'] = array();
            $data['expanded'] = true;
        }
        return $data;
    }
}
