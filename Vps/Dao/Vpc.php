<?php
class Vps_Dao_Vpc extends Vps_Db_Table
{
    protected $_name = 'vpc_data';
    protected $_primary = 'component_id';
    
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
