<?php
class Vpc_Root_TreeCache extends Vpc_TreeCache_TablePage
{
    protected $_tableName = 'Vps_Dao_Pages';
    protected $_componentClass = 'row';
    protected $_joinTreeCache = false;

    protected function _init()
    {
        $this->_showInMenu = new Zend_Db_Expr('!t.hide');
        parent::_init();
    }
    protected function _getWhere()
    {
        return array('id!=8');
    }

    public function createRoot()
    {
        $this->createMissingChilds();
        $pages = $this->_table->fetchAll();

        $filenames = array();
        $parents = array();
        foreach ($pages as $row) {
            $filenames[$row->id] = $row->filename;
            $parents[$row->id] = $row->parent_id;
        }
        foreach ($pages as $row) {
            if ($row->is_home) {
                $url = '/';
                $parentUrl = new Zend_Db_Expr('NULL');
            } else {
                $parentId = $row->parent_id;
                $url = '/'.$row->filename;
                $parentUrl = '';
                while ($parentId) {
                    $url = '/'.$filenames[$parentId].$url;
                    $parentUrl = '/'.$filenames[$parentId].$parentUrl;
                    $parentId = $parents[$parentId];
                }
                if ($parentUrl == '') $parentUrl = '/';
            }
            $url = $this->_cache->getAdapter()->quote($url);
            $parentUrl = $this->_cache->getAdapter()->quote($parentUrl);
            $componentId = $this->_cache->getAdapter()->quote($row->id);
            $this->_cache->getAdapter()->query("UPDATE vps_tree_cache SET
                url=$url, url_preview=$url, url_match=$url, url_match_preview=$url,
                url_pattern=$url, tree_url=$url, tree_url_pattern=$url,
                parent_url=$parentUrl
                WHERE component_id=$componentId");
        }
    }

    protected function _getSelectFields()
    {
        $fields = parent::_getSelectFields();
        $fields['parent_component_id'] = 'parent_id';
        $fields['component_id'] = 'id';
        $fields['db_id'] = 'id';
        unset($fields['url']);
        unset($fields['url_preview']);
        unset($fields['url_match']);
        unset($fields['url_match_preview']);
        unset($fields['parent_url']);
        unset($fields['url_pattern']);
        unset($fields['tree_url']);
        unset($fields['tree_url_pattern']);
        return $fields;
    }

    protected function _updateUrls($tcRow, Vps_Db_Table_Row_Abstract $row)
    {
        if ($row->is_home) {
            $tcRow->url = '/';
            $tcRow->url_match = '/';
            $tcRow->tree_url = '/';
            $tcRow->parent_url = null;
        } else {
            $parent = $tcRow->findParentComponent();
            if ($parent) {
                $tcRow->url = $parent->tree_url.'/'.$row->filename;
                $tcRow->url_match = $parent->tree_url.'/'.$row->filename;
                $tcRow->tree_url = $parent->tree_url.'/'.$row->filename;
                $tcRow->parent_url = $parent->tree_url;
            } else {
                $tcRow->url = '/'.$row->filename;
                $tcRow->url_match = '/'.$row->filename;
                $tcRow->tree_url = '/'.$row->filename;
                $tcRow->parent_url = '/';
            }
        }
        $tcRow->url_pattern = $tcRow->url;
        $tcRow->tree_url_pattern = $tcRow->url;
    }

    public function onUpdateRow(Vps_Db_Table_Row_Abstract $row)
    {
        if ($row->getTable() instanceof $this->_table) {
            $tcRow = $this->_cache->find($row->id)->current();
            if ($tcRow->parent_component_id != $row->parent_id) {
                $where = array();
                $id = $this->_cache->getAdapter()->quote($tcRow->component_id);
                $where[] = "component_id=$id OR component_id LIKE CONCAT($id, '\\_%')
                        OR component_id LIKE CONCAT($id, '-%')";
                $this->_cache->delete($where);
                $this->onInsertRow($row);
                return;
            }
        }
        parent::onUpdateRow($row);
    }
}
