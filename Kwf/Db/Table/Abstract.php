<?php
/**
 * @internal
 */
abstract class Kwf_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
    private $_dao;
    protected $_rowClass = 'Kwf_Db_Table_Row';
    protected $_rowsetClass = 'Kwf_Db_Table_Rowset';

    //_setupAdapter nicht ausführen, wir machen das besser lazy in _setupDatabaseAdapter
    protected function _setAdapter($db)
    {
        $this->_db = $db;
        return $this;
    }

    protected function _setupDatabaseAdapter()
    {
        //instead of setDefaultAdapter - this one lazy loads
        if (! $this->_db) {
            $this->_db = Kwf_Registry::get('db');
        } else if (is_string($this->_db)) {
            $this->_db = Kwf_Registry::get('dao')->getDb($this->_db);
        }
    }

    protected function _setupMetadata()
    {
        //hier drinnen damits nur gemacht wird sobald di erste Table erstellt wird
        if (!self::getDefaultMetadataCache()) {
            $frontendOptions = array(
                'automatic_serialization' => true,
                'write_control' => false
            );
            if (extension_loaded('apc') && php_sapi_name() != 'cli') {
                $backendOptions = array();
                $backend = 'Apc';
            } else {
                $backendOptions = array(
                    'cache_dir' => 'cache/model',
                    'file_name_prefix' => 'dbtable'
                );
                $backend = 'File';
            }
            $cache = Kwf_Cache::factory('Core', $backend, $frontendOptions, $backendOptions);
            self::setDefaultMetadataCache($cache);
        }

        parent::_setupMetadata();
    }

    public function setDao($dao)
    {
        $this->_dao = $dao;
    }

    public function getDao()
    {
        return $this->_dao;
    }

    public function select()
    {
        return new Kwf_Db_Table_Select($this);
    }

    //Overridden for better performance if Pdo Adatper is used
    //avoids parsing sql in Zend_Db_Statement::_stripQuoted which is slow
    protected function _fetch(Zend_Db_Table_Select $select)
    {
        if ($this->_db instanceof Zend_Db_Adapter_Pdo_Abstract) {
            $sql = $select->assemble();
            $conn = $this->_db->getConnection();
            $queryId = $this->_db->getProfiler()->queryStart($sql);
            $stmt = $conn->query($sql);
            $this->_db->getProfiler()->queryEnd($queryId);
            $data = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        } else {
            return parent::_fetch($select);
        }
    }

}
