<?php
class Vps_Dao_ComponentField extends Vps_Model_Db
{
    protected $_table = 'vpc_data';

    protected function _init()
    {
        parent::_init();
        $this->_siblingModels[] = new Vps_Model_Field(array(
            'fieldName' => 'data'
        ));
    }
    
    // TODO
    // Kopie von Vpc_Table
    public function find($id)
    {
        $ret = parent::find($id);
        if (!$ret->count()) {
            $defaults = array_combine($this->_cols, array_fill(0, count($this->_cols), null));
            $ret = new $this->_rowsetClass(array(
                'table'     => $this,
                'data'      => array($defaults),
                'readyOnly' => false,
                'rowClass'  => $this->_rowClass,
                'stored'    => false
            ));
            $ret->current()->component_id = $id;
        }
        return $ret;
    }
}
