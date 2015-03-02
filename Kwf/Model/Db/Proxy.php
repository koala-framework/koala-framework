<?php
/**
 * Proxy das standard-mäßig ein Db-Model mit tabellennamen _table
 * erstellt.
 *
 * @package Model
 */
class Kwf_Model_Db_Proxy extends Kwf_Model_Proxy
{
    protected $_table;
    public function __construct(array $config = array())
    {
        if (!isset($config['proxyModel'])) {
            if (isset($config['table'])) {
                $table = $config['table'];
            } else {
                $table = $this->_table;
            }
            if (!$table) {
                throw new Kwf_Exception('You must specify a table (protected _table or config table) or a proxyModel');
            }
            $config['proxyModel'] = new Kwf_Model_Db(
                array(
                    'table' => $table,
                    'hasDeletedFlag' => $this->_hasDeletedFlag
                )
            );
        }
        parent::__construct($config);
    }

    public function getAdapter()
    {
        return $this->getProxyModel()->getAdapter();
    }
}
