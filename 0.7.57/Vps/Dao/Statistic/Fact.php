<?php
class Vps_Dao_Statistic_Fact extends Vps_Db_Table
{
    protected $_rowClass = 'Vps_Dao_Statistic_Row_Fact';
    
    public function __construct($tablename, $config = array())
    {
        $config[self::NAME] = $tablename;
        $config[self::ADAPTER] = Zend_Registry::get('dao')->getDb('statistic');
        
        parent::__construct($config);
        
        $info = $this->info();
        foreach ($info['cols'] as $col) {
            if (substr($col, 0, 2) == 'D_') {
                $this->_referenceMap[$col] = array(
                    'columns' => $col,
                    'refTableClass' => 'Vps_Dao_Statistic_Dimension',
                    'refColumns' => $col
                );
            }
        }
    }
    
    public function find()
    {
        $args = func_get_args();
        $where = array('CONCAT(D_Datum_Monat, D_Betrieb) = ?' => $args[0]);
        return $this->fetchAll($where);
    }

    protected function _setupPrimaryKey()
    {
        $this->_primary = array(1 => 'id');
    }
}
