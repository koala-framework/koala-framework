<?php
abstract class Vpc_Table extends Vps_Db_Table
{
    protected $_primary = array('page_id', 'component_key');
    private $_invisibleMode = false;
    
    public function setInvisibleMode($mode)
    {
        $this->_invisibleMode = $mode;
    }
    
    protected function _fetch($where = null, $order = null, $count = null, $offset = null)
    {
        if (is_array($where) && $this->_invisibleMode && isset($where['visible = ?'])) {
            unset($where['visible = ?']);
        }
        return parent::_fetch($where, $order, $count, $offset);
    }
    

}
