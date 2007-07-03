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
        $componentId = $this->createPage(0);
        if ($componentId > 0) {
            $this->savePageName($componentId, 'Home');
            $this->_pageData = null;
            return $this->retrieveRootPageData();
        }

        throw new Vps_Exception('Could not find nor create Root Page');
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

    public function savePageName($componentId, $name)
    {
        $pageData = $this->retrievePageData($componentId);
        $row = $this->fetchRow('id = ' . $pageData['id']);
        $row->name = $name;
        $row->filename = $row->getUniqueString($name, 'filename', 'parent_id = ' . $pageData['parent_id']);
        $row->save();
        $this->_pageData = null;
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

    public function saveVisible($componentId, $visible)
    {
        $data = $this->retrievePageData($componentId);
        if (!empty($data)) {
            $row = $this->fetchRow('id = ' . $data['id']);
            $row->visible = $visible ? '1' : '0';
            $row->save();
            $this->_pageData = null;
            return true;
        }
        return false;
    }

    public function createPage($parentComponentId, $type = '')
    {
        // Leere Komponente hinzufügen
        $table = new Vps_Dao_Components();
        $componentId = $table->addComponent();

        // Eintrag in Pages-Tabelle
        if ($parentComponentId > 0) {
            $parentData = $this->retrievePageData($parentComponentId);
            if (empty($parentData)) {
                $parentData = $this->retrieveRootPageData();
            }
            $parentId = $parentData['id'];
            $position = sizeof($this->retrieveChildPagesData($parentId)) + 1;
            $name = 'New Page';
            $filename = 'newpage';
            $type = $parentData['type'] != '' ? $parentData['type'] : $type;
        } else {
            foreach ($this->_retrievePageData() as $d) {
                if ($d['parent_id'] == '0') {
                    throw new Vps_Exception('Cannot create RootPage because already existing.');
                }
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
        $this->insert($insert);

        $this->_pageData = null;
        return (int)$componentId;
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
        $rows = $this->delete($where);

        // Daten zurücksetzen
        $this->_pageData = null;
        return $rows;
    }

    public function movePage($sourceComponentId, $targetComponentId, $point, $type = '')
    {
        $sourceData = $this->retrievePageData($sourceComponentId);
        $targetData = $this->retrievePageData($targetComponentId);
        if ($point == 'append') {
            $parentId = $targetData['id'];
            $position = '1';
        } else {
            $parentData = $this->retrieveParentPageData($targetData['component_id']);
            $parentId = $parentData['id'];
            $siblings = $this->retrieveChildPagesData($parentData['component_id']);
            for ($x=0; $x<sizeof($siblings); $x++) {
                if ($siblings[$x]['id'] == $targetData['id']) {
                    if ($point == 'above') {
                        $position = $x;
                    } else if ($point == 'below') {
                        $position = $x + 2;
                    }
                }
            }
        }
        $row = $this->fetchRow('id = ' . $sourceData['id']);
        $row->parent_id = $parentId;
        $row->type = $targetData['type'] != '' ? $targetData['type'] : $type;
        $row->save();
        $row->numberize('position', $position, 'parent_id = ' . $parentId);

        $this->_pageData = null;
        return true;
    }
}