<?php
class Vpc_TreeCache_Page extends Vpc_TreeCache_TablePage
{
    protected $_tableName = 'Vps_Dao_Pages';
    protected $_componentClass = 'row';
    protected $_idSeparator = false;
    protected $_loadTableFromComponent = false;
    private $_pageData;
    private $_pageChilds;
    private $_pageFilename;
    private $_pageHome;

    protected function _init()
    {
        parent::_init();
        $select = new Zend_Db_Select($this->_db);
        $select->from('vps_pages', array('id', 'parent_id', 'component',
                                    'filename', 'hide', 'type', 'name', 'is_home'));
        $select->order('pos');
        if (!Zend_Registry::get('config')->showInvisible) {
            $select->where('visible = ?', 1);
        }

        $this->_pageData = array();
        $this->_pageParent = array();
        $this->_pageFilename = array();
        $this->_pageHome = 0;
        foreach ($select->query()->fetchAll() as $row) {
            $this->_pageData[$row['id']] = $row;
            $parentId = $row['parent_id'];
            if (is_null($parentId)) $parentId = 0;
            $this->_pageChilds[$parentId][] = $row['id'];
            $this->_pageFilename[$parentId][$row['filename']] = $row['id'];
            $this->_pageComponent[$parentId][$row['component']][] = $row['id'];
            if ($row['is_home']) $this->_pageHome = $row['id'];
        }
    }
    
    public function getChildIds($parentData, $constraints)
    {
        $ret = Vpc_TreeCache_Abstract::getChildIds($parentData, $constraints);
        $constraints = Vpc_TreeCache_Abstract::_formatConstraints($parentData, $constraints);
        if (is_null($constraints)) return $ret;

        if ($parentData instanceof Vps_Component_Data_Root) {
            $parentId = 0;
        } else {
            $parentId = $parentData->componentId;
        }
        $pageIds = array();

        if (isset($constraints['home']) && $constraints['home']) {
            if (isset($constraints['filename']) || isset($constraints['componentClass'])) {
                throw new Vps_Exception("Can't use contraint filename or componentClass together with home");
            }
            $pageIds[] = $this->_pageHome;
        } else if (isset($constraints['filename'])) {
            if (isset($constraints['componentClass'])) {
                throw new Vps_Exception("Can't use contraint filename and componentClass together");
            }
            if (!isset($this->_pageFilename[$parentId][$constraints['filename']])) {
                return $ret;
            }
            $pageIds[] = $this->_pageFilename[$parentId][$constraints['filename']];
        } else if (isset($constraints['componentClass'])) {
            $constraintClasses = $constraints['componentClass'];
            if (!is_array($constraintClasses)) {
                $constraintClasses = array($constraintClasses);
            }
            if (!$constraintClasses) return $ret;
            $childClasses = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
            $keys = array();
            foreach ($constraintClasses as $constraintClass) {
                $key = array_search($constraintClass, $childClasses);
                if ($key) $keys[] = $key;
            }
            if (!$keys) return $ret;

            foreach ($keys as $key) {
                if (isset($this->_pageComponent[$parentId][$key])) {
                    foreach ($this->_pageComponent[$parentId][$key] as $page) {
                        $pageIds[] = $page;
                    }
                }
            }
        } else {
            if (isset($this->_pageChilds[$parentId])) {
                $pageIds = $this->_pageChilds[$parentId];
            }
        }

        foreach ($pageIds as $pageId) {
            $page = $this->_pageData[$pageId];
            if (isset($constraints['type']) && $constraints['type'] != $page['type']) {
                continue;
            }
            if (isset($constraints['showInMenu']) && $constraints['showInMenu'] == $page['hide']) {
                continue;
            }
            $ret[] = $page['id'];
        }
        return $ret;
    }

    public function getChildData($parentData, $id)
    {
        if (isset($this->_pageData[$id])) {
            return $this->_createData($this->_formatConfig($parentData, $this->_pageData[$id]));
        }
        return Vpc_TreeCache_Abstract::getChildData($parentData, $id);
    }

    protected function _formatConfig($parentData, $page)
    {
        $data = array();
        $data['filename'] = $page['filename'];
        $data['rel'] = '';
        $data['name'] = $page['name'];
        $data['isPage'] = true;
        $data['componentId'] = $page['id'];
        $data['componentClass'] = $this->_getChildComponentClass($page['component']);
        $data['row'] = (object)$page;
        $data['id'] = $page['id'];
        if ($parentData instanceof Vps_Component_Data_Root && $page['parent_id']) {
            $data['parent'] = Vps_Component_Data_Root::getInstance()
                                    ->getComponentById($page['parent_id']);
        } else {
            $data['parent'] = $parentData;
        }
        return $data;
    }
}
