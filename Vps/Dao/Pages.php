<?php
class Vps_Dao_Pages extends Vps_Db_Table
{
    protected $_name = 'vps_pages';
    private $_pageData = null;
    private $_decoratorData = null;

    public function retrievePageData($componentId, $throwError = true)
    {
        $data = $this->_retrievePageData();
        if ($throwError && !isset($data[$componentId])) {
            throw new Vps_ClientException('Page width id "' . $componentId . '" not found');
        }
        return isset($data[$componentId]) ? $data[$componentId] : array();
    }

    public function retrieveParentPageData($componentId)
    {
        $data = $this->_retrievePageData();
        if (isset($data[$componentId])) {
            $parentId = $data[$componentId]['parent_id'];
            if (isset($data[$parentId])) {
                return $data[$parentId];
            }
        }

        throw new Vps_ClientException('ParentPage width id "' . $componentId . '" not found');
    }

    public function retrieveChildPagesData($componentId, $type = null)
    {
        $return = array();
        $data = $this->_retrievePageData();
        if (isset($data[$componentId])) {
            $parentId = $data[$componentId]['id'];
            foreach ($data as $d) {
                if ($parentId && $d['parent_id'] == $parentId) {
                    if (is_null($type) || $d['type'] == $type) {
                        $return[] = $d;
                    }
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
        $row = $this->fetchRow('parent_id = 0');
        if (!$row) {
            $id = $this->createPage(0);
            if ($id > 0) {
                $this->savePageName($id, 'Home');
                $this->_pageData = null;
                return $this->retrieveRootPageData();
            }
        } else {
            return null;
        }

        throw new Vps_Exception('Could not find nor create Root Page');
    }

    private function _retrievePageData()
    {
        if ($this->_pageData == null) {
            $sql = '
                SELECT c.id component_id, c.component, c.page_id, p.id, p.parent_id, p.type, p.visible, p.name, p.filename
                FROM vps_components c
                LEFT JOIN vps_pages p
                ON c.id=p.component_id
                ORDER BY p.position
            ';
            $this->_pageData = $this->getAdapter()->fetchAssoc($sql);
        }
        return $this->_pageData;
    }

    public function retrieveDecoratorData($id)
    {
        if ($this->_decoratorData == null) {
            $this->_decoratorData = array();
            $sql = '
                SELECT id, decorator, page_id
                FROM vps_decorators
            ';
            $data = $this->getAdapter()->fetchAll($sql);
            foreach ($data as $d) {
                $pageId = $d['page_id'];
                unset($d['page_id']);
                $this->_decoratorData[$pageId][] = $d['decorator'];
            }
        }
        return isset($this->_decoratorData[$id]) ? $this->_decoratorData[$id] : array() ;
    }

    public function findPagesByClass($class)
    {
        $return = array();
        foreach ($this->_retrievePageData() as $componentId => $data) {
            if ($data['component'] == $class) {
                $return[] = $data['page_id'];
            }
        }
        return $return;
    }

    public function saveDecorators($id, $decorators)
    {
        $delete = array();
        $add = array();
        $existingDecorators = $this->retrieveDecoratorData($id);
        foreach ($existingDecorators as $d) {
            if (!in_array($d, $decorators)) {
                $delete[] = $d;
            }
        }
        foreach ($decorators as $d) {
            if (!in_array($d, $existingDecorators)) {
                $add[] = $d;
            }
        }
        $sql = "DELETE FROM vps_decorators WHERE page_id='$id' AND decorator IN ('" . implode("', '", $delete) . "')";
        $this->getAdapter()->query($sql);
        foreach ($add as $d) {
            $sql = "INSERT INTO vps_decorators SET page_id='$id', decorator='$d'";
            $this->getAdapter()->query($sql);
        }
        $this->_decoratorData = null;
        return $this->retrieveDecoratorData($id);
    }

    public function insert(array $data)
    {
        $parentId = $data['parent_id'];
        $name = $data['name'];
        $type = '';
        if ((int)$parentId == 0) {
            $type = $parentId;
            $row = $this->fetchRow('parent_id = 0');
            $parentId = $row->id;
        }

        $id = $this->createPage($parentId, $type, $name);
        $this->savePageName($id, $name);
        return $id;
    }
    
    public function createPage($parentId, $type = '')
    {
        $this->_pageData = null;

        // Leere Komponente hinzufügen
        $table = new Vps_Dao_Components();
        $componentId = $table->addComponent();

        // Eintrag in Pages-Tabelle
        if ($parentId > 0) {
            $position = 1;
            $name = 'New Page';
            $filename = 'newpage';
            $parentRow = $this->find($parentId)->current();
            $type = $parentRow->type != '' ? $parentRow->type : $type;
        } else {
            if ($this->fetchRow('parent_id = 0')) {
                throw new Vps_Exception('Cannot create RootPage because already existing.');
            }
            $position = 1;
            $parentId = 0;
            $name = 'Home';
            $filename = 'home';
            $type = '';
        }

        $insert = array();
        $insert['name'] = $name;
        $insert['filename'] = $filename;
        $insert['component_id'] = $componentId;
        $insert['parent_id'] = $parentId;
        $insert['position'] = $position;
        $insert['type'] = $type;
        return parent::insert($insert);
    }
    
    public function savePageName($id, $name)
    {
        $row = $this->find($id)->current();
        $row->name = $name;
        $row->filename = $row->getUniqueString($name, 'filename', 'parent_id = ' . $row->parent_id);
        $row->save();
        $this->_pageData = null;
    }

    public function deletePage($componentId)
    {
        $data = $this->retrievePageData($componentId);

        // Unterseiten rekursiv löschen
        $childPageData = $this->retrieveChildPagesData($componentId);
        foreach ($childPageData as $cd) {
            $this->deletePage($cd['component_id']);
        }

        // Komponenten löschen
        $table = $this->getDao()->getTable('Vps_Dao_Components');
        $table->deleteComponent($componentId);

        // Eintrag in Pages-Tabelle löschen
        $where = $this->getAdapter()->quoteInto('component_id = ?', $componentId);
        $rows = parent::delete($where);

        // Daten zurücksetzen
        $this->_pageData = null;
        return $rows;
    }

    public function delete($where)
    {
        $row = $this->fetchRow($where);
        if ($row) {
            return $this->deletePage($row->component_id);
        }
    }

}