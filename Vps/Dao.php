<?php
class Vps_Dao
{
    private $_config;
    private $_tables = array();
    private $_db = array();
    private $_pageData = array();
    
    public function __construct(Zend_Config $config)
    {
        $this->_config = $config;
    }

    public function getTable($tablename)
    {
        if (!isset($this->_tables[$tablename])) {
            try {
              Zend_Loader::loadClass($tablename);
              $this->_tables[$tablename] = new $tablename(array('db'=>$this->getDb()));
              if ($this->_tables[$tablename] instanceof Vps_Db_Table) {
                  $this->_tables[$tablename]->setDao($this);
              }
            } catch (Zend_Exception $e){
              throw new Vps_Dao_Exception('Dao not found: ' . $e->getMessage());
            }
        }
        return $this->_tables[$tablename];
    }
    
    public function getDb($db = 'web')
    {
        if (!isset($this->_db[$db])) {
            if(!isset($this->_config->$db)) {
                throw new Vps_Dao_Exception("Connection \"$db\" in config.db.ini not found.
                        Please add $db.host, $db.username, $db.password and $db.dbname under the sction [database].");
            }
            $dbConfig = $this->_config->$db->toArray();
            $this->_db[$db] = Zend_Db::factory('PDO_MYSQL', $dbConfig);
        }
        return $this->_db[$db];
    }
    

}
