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
            $dbConfig = $this->_config->$db->asArray();
            $this->_db[$db] = Zend_Db::factory('PDO_MYSQL', $dbConfig);
        }
        return $this->_db[$db];
    }
    
    function getPageData($componentId)
    {
        $data = $this->_getPageData();
        return isset($data[$componentId]) ? $data[$componentId] : array();
    }

    function getParentPageData($componentId)
    {
        $data = $this->_getPageData();
        if (isset($data[$componentId])) {
            $parentId = $data[$componentId]['parent_id'];
            if (isset($data[$parentId])) {
                return $data[$parentId];
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    function getChildPagesData($componentId)
    {
        $return = array();

        $data = $this->_getPageData();
        $parent_id = $data[$componentId]['id'];
        foreach ($data as $d) {
            if ($d['parent_id'] == $parent_id) {
                $return[] = $d;
            }
        }
        return $return;
    }

    function getRootPageData()
    {
        foreach ($this->_getPageData() as $d) {
            if ($d['parent_id'] == '0') {
                return $d;
            }
        }
    }
    
    private function _getPageData()
    {
        if (empty($this->_pageData)) {
            $sql = '
                SELECT components.id component_id, components.component, pages.id, pages.parent_id, pages.name, pages.filename 
                FROM components
                LEFT JOIN pages
                ON components.id=pages.component_id
                ORDER BY pages.nr
            ';
            $this->_pageData = $this->getDb()->fetchAssoc($sql);
        }
        return $this->_pageData;
    }

}
