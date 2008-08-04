<?php
class Vps_Component_Model implements Vps_Model_Interface 
{
    protected $_rowClass = 'Vps_Component_Model_Row';
    protected $_rowsetClass = 'Vps_Component_Model_Rowset';
    protected $_constraints = array(
        'generator' => 'page',
        'ignoreVisible' => true
    );
    
    public function createRow(array $data=array()) {
        throw new Vps_Exception('Not implemented yet.');
    }
    
    public function find($id)
    {
        return new $this->_rowsetClass(array(
            'data' => array(Vps_Component_Data_Root::getInstance()->getComponentById($id, $this->_constraints)),
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }
    
    public function fetchAll($where=null, $order=null, $limit=null, $start=null)
    {
        $root = Vps_Component_Data_Root::getInstance();
        if ($where['parent']) {
            $constraints = $this->_constraints;
            if (isset($where['type'])) {
                $constraints['type'] = $where['type'];
            }
            $page = $root->getComponentById($where['parent'], $constraints);
            $rowset = $page->getChildComponents($this->_constraints);
        } else {
            $rowset = array($root);
        }
        return new $this->_rowsetClass(array(
            'data' => $rowset,
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }
    
    public function fetchCount($where = array())
    {
        throw new Vps_Exception('Not implemented yet.');
    }
    
    public function getPrimaryKey()
    {
        return 'componentId';
    }
    
    public function getTable()
    {
        return new Vps_Dao_Pages();
    }
}