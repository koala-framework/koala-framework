<?php
class Vps_Dao_Statistic_Dimension extends Vps_Db_Table
{
    protected $_rowClass = 'Vps_Dao_Statistic_Row_Dimension';
    protected $_dependentTables = array('Vps_Dao_Statistic_Fact');
    
    public function __construct($tablename, $config = array())
    {
        $this->_name = $tablename;
        $config[self::ADAPTER] = Zend_Registry::get('dao')->getDb('statistic');
        parent::__construct($config);
    }
}
