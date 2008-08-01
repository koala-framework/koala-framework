<?php
class Vps_Component_Model implements Vps_Model_Interface 
{
    protected $_rowClass = 'Vps_Component_Model_Row';
    protected $_rowsetClass = 'Vps_Component_Model_Rowset';
    
    public function createRow(array $data=array()) {
        throw new Vps_Exception('Not implemented yet.');
    }
    
    public function find($id)
    {
        return new $this->_rowsetClass(array(
            'data' => array(Vps_Component_Data_Root::getInstance()->getComponentById($id)),
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }
    
    public function fetchAll($where=null, $order=null, $limit=null, $start=null)
    {
        $root = Vps_Component_Data_Root::getInstance();
        if ($where['parent']) {
            $page = $root->getComponentById($where['parent'], array('ignoreVisible' => true)); 
            $rowset = $page->getChildComponents(array('generator' => 'page', 'ignoreVisible' => true));
        } else {
            $rowset = array($root);
        }
        return new $this->_rowsetClass(array(
            'data' => $rowset,
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }
    
    public function fetchCount($where = array()) {
        throw new Vps_Exception('Not implemented yet.');
    }
    
    public function getPrimaryKey() {
        return 'componentId';
    }
}