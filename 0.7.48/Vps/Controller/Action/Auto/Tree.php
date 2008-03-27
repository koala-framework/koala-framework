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
}
