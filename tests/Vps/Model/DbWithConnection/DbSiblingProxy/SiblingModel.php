<?php
class Vps_Model_DbWithConnection_DbSiblingProxy_SiblingModel extends Vps_Model_Db
{
    protected $_referenceMap = array(
        'Master' => array(
            'column' => 'master_id',
            'refModelClass' => 'Vps_Model_DbWithConnection_DbSiblingProxy_ProxyModel'
        )
    );
    private $_tableName;

    public function __construct($config = array())
    {
        $this->_tableName = 'sibling'.uniqid();
        $config['table'] = $this->_tableName;
        Vps_Registry::get('db')->query("CREATE TABLE {$this->_tableName} (
                `master_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `baz` VARCHAR( 200 ) NOT NULL
            ) ENGINE = INNODB");
        Vps_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (master_id, baz) VALUES ('1', 'aha')");

        parent::__construct($config);
    }

    public function dropTable()
    {
        Vps_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }

    public function clearRows()
    {
        $this->_rows = array();
    }

}
