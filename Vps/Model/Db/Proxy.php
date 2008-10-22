<?php
/**
 * Proxy das standard-mäßig ein Db-Model mit tabellennamen _table
 * erstellt.
 */
class Vps_Model_Db_Proxy extends Vps_Model_Proxy
{
    protected $_table;
    public function __construct(array $config = array())
    {
        if (!isset($config['proxyModel'])) {
            $config['proxyModel'] = new Vps_Model_Db(
                array(
                    'table' => $this->_table,
                    'default' => $this->_default
                )
            );
        }
        parent::__construct($config);
    }

}
