<?php
class Vps_Model_FnF extends Vps_Model_Abstract
{
    protected $_rowClass = 'Vps_Model_FnF_Row';
    protected $_rowsetClass = 'Vps_Model_FnF_Rowset';

    public function find($id)
    {
        return new $this->_rowsetClass(array(
            'model' => $this,
            'rowClass' => $this->_rowClass,
            'data' => array(array('id'=>$id))
        ));
    }
}
