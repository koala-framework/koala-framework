<?php
abstract class Vps_Db_Table extends Zend_Db_Table
{
    private $_dao;
    protected $_rowClass = 'Vps_Db_Table_Row';
    protected $_rowsetClass = 'Vps_Db_Table_Rowset';

    public function setDao($dao)
    {
        $this->_dao = $dao;
    }

    public function getDao()
    {
        return $this->_dao;
    }

    public function numberize($id, $fieldname, $value, $where = '')
    {
        $row = $this->find($id)->current();
        if ($row) {
            return $row->numberize($fieldname, $value, $where);
        } else {
            return false;
        }
    }
    
    public function numberizeAll($fieldname, $where = array())
    {
        // Alle Elemente selecten
        $rows = $this->fetchAll($where, $fieldname);
        $i = 1;
        foreach ($rows as $row) {
            if ($row->$fieldname != $i) {
                $row->$fieldname = $i;
                $row->save();
            }
            $i++;
        }

    }
}
?>
