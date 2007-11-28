<?php
class Vps_Dao_Pages extends Vps_Db_Table
{
    protected $_name = 'vps_pages';
    private $_pageData = null;
    private $_decoratorData = null;
    private $_showInvisible = false;

    public function showInvisible($show)
    {
        $this->_showInvisible = $show;
    }

    public function retrievePageData($id, $throwError = true)
    {
        $data = $this->_retrievePageData();
        if ($throwError && !isset($data[$id])) {
            throw new Vps_ClientException('Page width id "' . $id . '" not found');
        }
        return isset($data[$id]) ? $data[$id] : array();
    }

    public function retrieveParentPageData($id)
    {
        $data = $this->_retrievePageData();
        if (isset($data[$id])) {
            $parentId = $data[$id]['parent_id'];
            if (isset($data[$parentId])) {
                return $data[$parentId];
            }
        }
        return null;
    }

    public function retrieveChildPagesData($id = null, $type = null)
    {
        $return = array();
        $data = $this->_retrievePageData();
        if (is_null($id)) {
            $parentId = null;
        } else {
            if (isset($data[$id])) {
                $parentId = $data[$id]['id'];
            } else {
                $parentId = -1;
            }
        }
        foreach ($data as $d) {
            if ($d['parent_id'] == $parentId && (is_null($type) || $d['type'] == $type)) {
                $return[] = $d;
            }
        }
        return $return;
    }

    public function retrieveHomePageData()
    {
        foreach ($this->_retrievePageData() as $d) {
            if ($d['is_home'] == 1) {
                return $d;
            }
        }
        throw new Vps_Exception('Could not find Root Page');
    }

    private function _retrievePageData()
    {
        if ($this->_pageData == null) {
            $where = $this->_showInvisible ? '' : 'WHERE visible=1';
            $sql = '
                SELECT id, parent_id, type, is_home, visible, hide, name, filename, component_class
                FROM vps_pages
                ' . $where . '
                ORDER BY pos
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
        foreach ($this->_retrievePageData() as $id => $data) {
            if ($data['component_class'] == $class) {
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
        if ((int)$data['parent_id'] == 0) {
            $data['type'] = $data['parent_id'];
            $data['parent_id'] = null;
        } else {
            $parentRow = $this->retrievePageData($data['parent_id']);
            $data['type'] = $parentRow['type'];
        }

        $data['is_home'] = 0;
        $data['filename'] = '';
        $data['visible'] = 0;
        $data['pos'] = 1;
        $id = parent::insert($data);
        if ($id) {
            $row = $this->find($id)->current();
            $where = array();
            $where['type = ?'] = $data['type'];
            if (!$row->parent_id) {
                $where['parent_id IS NULL'] = '';
            } else {
                $where['parent_id = ?'] = $row->parent_id;
            }
            $row->filename = $row->getUniqueString($data['name'], 'filename', $where);
            $row->save();
            $row->numberize('pos', null, $where);
        }

        $this->_pageData = null;
        return $id;
    }

    public function savePageName($id, $name)
    {
        $row = $this->find($id)->current();
        $row->name = $name;
        $row->filename = $row->getUniqueString($name, 'filename', 'parent_id = ' . $row->parent_id);
        $row->save();
        $this->_pageData = null;
    }

    public function deletePage($id)
    {
        $this->_showInvisible = true;
        $data = $this->retrievePageData($id);

        // Unterseiten rekursiv löschen
        $childPageData = $this->retrieveChildPagesData($id);
        foreach ($childPageData as $cd) {
            $this->deletePage($cd['id']);
        }

        // Eintrag in Pages-Tabelle löschen
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $rows = parent::delete($where);

        // Daten zurücksetzen
        $this->_pageData = null;
        return $rows;
    }

    public function delete($where)
    {
        $row = $this->fetchRow($where);
        if ($row) {
            return $this->deletePage($row->id);
        }
    }

}