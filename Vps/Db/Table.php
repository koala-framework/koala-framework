<?php
abstract class Vps_Db_Table extends Zend_Db_Table
{
    private $_dao;
    protected $_rowClass = 'Vps_Db_Table_Row';
    
    public function setDao($dao)
    {
        $this->_dao = $dao;
    }
    
    public function getDao()
    {
        return $this->_dao;
    }

    public function numberize($where, $fieldname, $value, $limit = '')
    {
        $row = $this->fetchRow($where);
        if ($row) {
            return $row->numberize($fieldname, $value, $limit);
        } else {
            return false;
        }
    }
}
?>
