<?php
class Vps_Dao_Pages extends Vps_Db_Table_Abstract
{
    protected $_name = 'vps_pages';
    protected $_rowClass = 'Vps_Dao_Row_Page';
    private $_pageData = null;
    private $_showInvisible = false;

    protected function _setupFilters()
    {
        parent::_setupFilters();
        $this->_filters['filename'] = new Vps_Filter_Row_UniqueAscii();
        $this->_filters['filename']->setGroupBy('parent_id');
    }

    public function showInvisible($show)
    {
        $this->_showInvisible = $show;
    }

    public function retrievePageData($id, $throwError = true)
    {
        $data = $this->_retrievePageData();
        if ($throwError && !isset($data[$id])) {
            throw new Vps_Exception(trlVps('Page width id {0} not found', '\''.$id.'\''));
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

    public function savePageName($id, $name)
    {
        $row = $this->find($id)->current();
        $row->name = $name;
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

        // Dranhängende Komponente löschen
        Vpc_Admin::getInstance($data['component_class'])->delete($data['id']);

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

    public function insert($data)
    {
        $id = parent::insert($data);
        $where = array();
        $where['type = ?'] = $data['type'];
        if (!$data['parent_id']) {
            $where[] = 'parent_id IS NULL';
        } else {
            $where['parent_id = ?'] = $data['parent_id'];
        }
        $this->numberize('pos', null, $where);
        return $id;
    }
}
