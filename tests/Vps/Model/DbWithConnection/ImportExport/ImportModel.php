<?php
class Vps_Model_DbWithConnection_DbSibling_ImportModel extends Vps_Model_Db
{
    public function __construct($config = array())
    {
        $this->_tableName = 'import'.uniqid();
        $config['table'] = $this->_tableName;
        Vps_Registry::get('db')->query("CREATE TABLE {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `foo` VARCHAR( 200 ) NOT NULL ,
            `bar` VARCHAR( 200 ) NOT NULL
        ) ENGINE = INNODB");
        parent::__construct($config);
    }

    public function clearRows()
    {
        $this->_rows = array();
    }
}
