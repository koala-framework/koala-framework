<?php
abstract class Vps_Controller_Action_Auto_Tree extends Vps_Controller_Action_Auto_Synctree
{
    public function jsonDataAction()
    {
        $parentId = $this->_getParam('node');

        $this->_saveSessionNodeOpened($parentId, true);
        $this->_saveNodeOpened();

        $order = $this->_hasPosition ? 'pos' : null ;
        $rowset = $this->_table->fetchAll($this->_getWhere($parentId), $order);

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
}
