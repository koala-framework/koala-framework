<?php
class Vps_Model_DbWithConnection_SiblingRelationExpr_RelationDbModel extends Vps_Model_Db
{
    protected $_tableName;
    public function __construct()
    {
        $this->_tableName = 'relation'.uniqid();
        $config['table'] = $this->_tableName;
        Vps_Registry::get('db')->query("CREATE TABLE {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `master_id` INT NOT NULL ,
            `bar` VARCHAR( 200 ) NOT NULL
        ) ENGINE = INNODB");
        Vps_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, master_id, bar) VALUES ('1', 1, 'abcd')");
        Vps_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, master_id, bar) VALUES ('2', 1, 'bum')");

        parent::__construct($config);
    }

    public function dropTable()
    {
        Vps_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }

}
