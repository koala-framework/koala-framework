<?php
class Vps_Db_TablesModel extends Vps_Model_Data_Abstract
{
    protected $_dependentModels = array(
        'Fields' => 'Vps_Db_TableFieldsModel'
    );
    private $_db;
    protected $_primaryKey = 'table';
    protected $_rowClass = 'Vps_Db_TablesModel_Row';

    public function __construct(array $options = array())
    {
        if (isset($options['db'])) $this->_db = $options['db'];
        parent::__construct($options);
    }

    public function getDb()
    {
        if (isset($this->_db)) return $this->_db;
        return Vps_Registry::get('db');
    }

    protected function _init()
    {
        $tables = $this->getDb()->fetchCol('SHOW TABLES');
        $data = array();
        foreach ($tables as $t) {
            $data[] = array(
                'table' => $t
            );
        }
        $this->_data = $data;
    }

    public function getRows($select=null, $order=null, $limit=null, $start=null)
    {
        if (!is_object($select)) {
            $select = $this->select($select, $order, $limit, $start);
        }
        return new $this->_rowsetClass(array(
            'model' => $this,
            'dataKeys' => $this->_selectDataKeys($select, $this->_data)
        ));
    }

    public function getUniqueIdentifier()
    {
        throw new Vps_Exception("no unique identifier set");
    }

    public function delete(Vps_Model_Row_Interface $row)
    {
        parent::delete($row);
        $this->getDb()->query("DROP TABLE $row->table");
    }
}
