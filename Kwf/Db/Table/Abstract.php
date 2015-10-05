<?php
/**
 * @internal
 */
abstract class Kwf_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
    private $_dao;
    protected $_rowClass = 'Kwf_Db_Table_Row';
    protected $_rowsetClass = 'Kwf_Db_Table_Rowset';

    /**
     * Row-Filters für automatisch befüllte Spalten
     *
     * Anwendungsbeispiele:
     * _filters = 'filename' //verwendet autom. Kwf_Filter_Ascii
     * _filters = array('filename') //verwendet autom. Kwf_Filter_Ascii
     * _filters = array('pos')      //Kwf_Filter_Row_Numberize
     * _filters = array('pos' => 'MyFilter')
     * _filters = array('pos' => new MyFilter($settings))
     */
    protected $_filters = array();

    protected function _setup()
    {
        parent::_setup();
        $this->_setupFilters();
    }

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

    protected function _setupFilters()
    {
    }

    public function getFilters()
    {
        if (is_string($this->_filters)) $this->_filters = array($this->_filters);
        foreach($this->_filters as $k=>$f) {
            if (is_int($k)) {
                unset($this->_filters[$k]);
                $k = $f;
                if ($k == 'pos') {
                    $f = 'Kwf_Filter_Row_Numberize';
                } else {
                    $f = 'Kwf_Filter_Ascii';
                }
            }
            if (is_string($f)) {
                $f = new $f();
            }
            if ($f instanceof Kwf_Filter_Row_Abstract) {
                $f->setField($k);
            }
            $this->_filters[$k] = $f;
        }
        return $this->_filters;
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
