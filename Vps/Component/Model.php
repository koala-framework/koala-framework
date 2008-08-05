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
            if ($where['parent'] == 'root') {
                $rowset = array();
                foreach (Zend_Registry::get('config')->vpc->pageTypes->toArray() as $id => $name) {
                    $id = 'root-' . $id;
                    $rowset[] = new Vps_Component_Data_Category($id, $name);
                }
            } else {
                if (substr($where['parent'], 0, 5) == 'root-') {
                    $constraints['type'] = substr($where['parent'], 5);
                    $where['parent'] = 'root';
                }
                $page = $root->getComponentById($where['parent'], $this->_constraints);
                $rowset = $page->getChildComponents($constraints);
            }
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