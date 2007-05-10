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
    
    public function retrievePageData($componentId)
    {
        $data = $this->_retrievePageData();
        return isset($data[$componentId]) ? $data[$componentId] : array();
    }

    public function retrieveParentPageData($componentId)
    {
        $data = $this->_retrievePageData();
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

    public function retrieveChildPagesData($componentId)
    {
        $return = array();

        $data = $this->_retrievePageData();
        if (isset($data[$componentId])) {
            $parent_id = $data[$componentId]['id'];
            foreach ($data as $d) {
                if ($d['parent_id'] == $parent_id) {
                    $return[] = $d;
                }
            }
        }
        return $return;
    }

    public function retrieveRootPageData()
    {
        foreach ($this->_retrievePageData() as $d) {
            if ($d['parent_id'] == '0') {
                return $d;
            }
        }
    }
    
    private function _retrievePageData()
    {
        if (empty($this->_pageData)) {
            $sql = '
                SELECT components.id component_id, components.component, pages.id, pages.parent_id, pages.status, pages.name, pages.filename 
                FROM components
                LEFT JOIN pages
                ON components.id=pages.component_id
                ORDER BY pages.nr
            ';
            $this->_pageData = $this->getDb()->fetchAssoc($sql);
        }
        return $this->_pageData;
    }
    
    public function savePageName($componentId, $name)
    {
        $data = $this->retrievePageData($componentId);
        if (!empty($data)) {
            $table = $this->getTable('Vps_Dao_Pages');
            $row = $table->fetchRow('id = ' . $data['id']);
            $row->name = $name;
            // TODO: filename ableiten und speichern
            $row->save();
            unset($this->_pageData);
            return true;
        }
        return false;
    }

    public function savePageStatus($componentId, $status)
    {
        $data = $this->retrievePageData($componentId);
        if (!empty($data)) {
            $table = $this->getTable('Vps_Dao_Pages');
            $row = $table->fetchRow('id = ' . $data['id']);
            $row->status = $status ? '1' : '0';
            $row->save();
            unset($this->_pageData);
            return true;
        }
        return false;
    }

    public function createPage($parentComponentId)
    {
        $data = $this->retrievePageData($parentComponentId);
        if (!empty($data)) {
            // Leere Komponente hinzufügen
            $table = $this->getTable('Vps_Dao_Components');
            $componentId = $table->addComponent();

            // Eintrag in Pages-Tabelle
            $table = $this->getTable('Vps_Dao_Pages');
            $parentData = $this->retrievePageData($parentComponentId);
            if (empty($parentData)) {
                $parentData = $this->retrieveRootPageData();
            }
            $parentId = $parentData['id'];
            $nr = sizeof($this->retrieveChildPagesData($parentId)) + 1;
            
            $insert = array();
            $insert['name'] = 'New Page';
            $insert['filename'] = 'newpage';
            $insert['component_id'] = $componentId;
            $insert['parent_id'] = $parentId;
            $insert['nr'] = $nr;
            $table->insert($insert);
            
            unset($this->_pageData);
            return (int)$componentId;
        }
        return 0;
    }

    public function deletePage($componentId)
    {
        $data = $this->retrievePageData($componentId);
        if (!empty($data)) {
            
            // Unterseiten rekursiv löschen
            $childPageData = $this->retrieveChildPagesData($componentId);
            foreach ($childPageData as $cd) {
                $this->deletePage($cd['component_id']);
            }
            
            // Komponenten löschen
            $table = $this->getTable('Vps_Dao_Components');
            $table->deleteComponent($componentId);

            // Eintrag in Pages-Tabelle löschen
            $table = $this->getTable('Vps_Dao_Pages');
            $where = $table->getAdapter()->quoteInto('component_id = ?', $componentId);
            $rows = $table->delete($where);
            unset($this->_pageData);
            return $rows;
            
        }
        return 0;
    }

    public function movePage($sourceComponentId, $targetComponentId, $point)
    {
        $sourceData = $this->retrievePageData($sourceComponentId);
        $targetData = $this->retrievePageData($targetComponentId);
        if ($point == 'append') {
            $parentId = $targetData['id'];
            $nr = '1';
        } else {
            $parentData = $this->retrieveParentPageData($targetData['component_id']);
            $parentId = $parentData['id'];
            $siblings = $this->retrieveChildPagesData($parentData['component_id']);
            for ($x=0; $x<sizeof($siblings); $x++) {
                if ($siblings[$x]['id'] == $targetData['id']) {
                    if ($point == 'above') {
                        $nr = $x;
                    } else if ($point == 'below') {
                        $nr = $x + 2;
                    }
                }
            }
        }
        $table = $this->getTable('Vps_Dao_Pages');
        $row = $table->fetchRow('id = ' . $sourceData['id']);
        $row->parent_id = $parentId;
        $row->nr = $nr;
        $row->save();

        unset($this->_pageData);
    }

}
