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

    public function jsonDataAction()
    {
        $parentId = $this->_getParam('node');
        $this->_saveSessionNodeOpened($parentId, true);
        $this->_saveNodeOpened();

        if ($parentId) {
            $parentRow = $this->_model->getRow($parentId);
        } else {
            $parentRow = null;
        }
        $rows = $this->_fetchData($parentRow);
        $nodes = array();
        foreach ($rows as $row) {
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

        $select = $this->_model->select($this->_getTreeWhere($row));
        $select->whereEquals($this->_parentField, $row->{$this->_primaryKey});
        if ($this->_model->fetchCount($select)) {
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
        }
        return $data;
    }
}
