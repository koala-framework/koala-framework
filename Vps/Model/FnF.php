<?php
class Vps_Model_FnF extends Vps_Model_Abstract
{
    protected $_rowClass = 'Vps_Model_FnF_Row';
    protected $_rowsetClass = 'Vps_Model_FnF_Rowset';
    protected $_data = array();

    public function find($id)
    {
        $data = array();
        foreach ($this->_data as $d) {
            if (isset($d['id']) && $d['id'] == $id) {
                $data = array($d);
                break;
            }
        }
        return new $this->_rowsetClass(array(
            'model' => $this,
            'rowClass' => $this->_rowClass,
            'data' => $data
        ));
    }
    
    public function fetchAll($where=null, $order=null, $limit=null, $start=null)
    {
        return new $this->_rowsetClass(array(
            'model' => $this,
            'rowClass' => $this->_rowClass,
            'data' => $this->_data
        ));
    }
        
    public function setData(array $data)
    {
        $this->_data = $data;
    }
}
