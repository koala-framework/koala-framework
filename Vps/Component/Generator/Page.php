<?php
class Vpc_TreeCache_Page extends Vpc_TreeCache_Abstract
{
    protected $_tableName = 'Vps_Dao_Pages';
    protected $_componentClass = 'row';
    protected $_idSeparator = false;
    protected $_loadTableFromComponent = false;
    private $_pageData;
    private $_pageParent;
    private $_pageFilename;
    private $_pageComponentParent;
    private $_pageComponent;
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
        $this->_pageComponentParent = array();
        $this->_pageComponent = array();
        $this->_pageHome = 0;
        foreach ($select->query()->fetchAll() as $row) {
            $this->_pageData[$row['id']] = $row;
            $parentId = $row['parent_id'];
            if (is_null($parentId)) $parentId = 0;
            $this->_pageChilds[$parentId][] = $row['id'];
            $this->_pageFilename[$parentId][$row['filename']] = $row['id'];
            $this->_pageComponentParent[$parentId][$row['component']][] = $row['id'];
            $this->_pageComponent[$row['component']][] = $row['id'];
            if ($row['is_home']) $this->_pageHome = $row['id'];
        }
    }
    
    public function getChildIds($parentData, $constraints)
    {
        $ret = Vpc_TreeCache_Abstract::getChildIds($parentData, $constraints);
        $constraints = Vpc_TreeCache_Abstract::_formatConstraints($parentData, $constraints);
        if (is_null($constraints)) return $ret;
        if (isset($constraints['page']) && !$constraints['page']) return $ret;

        if ($parentData instanceof Vps_Component_Data_Root) {
            $parentId = 0;
        } else if ($parentData) {
            $parentId = $parentData->componentId;
        }
        $pageIds = array();

        if (isset($constraints['id'])) {
            if (isset($constraints['home']) || isset($constraints['filename']) || isset($constraints['componentClass'])) {
                throw new Vps_Exception("Can't use contraint home, filename or componentClass together with id");
            }
            if (isset($this->_pageData[$constraints['id']])) {
                $pageIds[] = $constraints['id'];
            }
        } else if (isset($constraints['home']) && $constraints['home']) {
            if (isset($constraints['filename']) || isset($constraints['componentClass'])) {
                throw new Vps_Exception("Can't use contraint filename or componentClass together with home");
            }
            if ($this->_pageHome) {
                $pageIds[] = $this->_pageHome;
            }
        } else if (isset($constraints['filename'])) {
            if (isset($constraints['componentClass'])) {
                throw new Vps_Exception("Can't use contraint filename and componentClass together");
            }
            if (!isset($parentId)) {
                throw new Vps_Exception("filename contraint only works with parentData");
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
                if (isset($parentId) && isset($this->_pageComponentParent[$parentId][$key])) {
                    $pageIds = array_merge($pageIds, $this->_pageComponentParent[$parentId][$key]);
                }
                if (!isset($parentId) && isset($this->_pageComponent[$key])) {
                    $pageIds = array_merge($pageIds, $this->_pageComponent[$key]);
                }
            }
        } else {
            if (isset($parentId) && isset($this->_pageChilds[$parentId])) {
                $pageIds = $this->_pageChilds[$parentId];
            }
            if (!isset($parentId)) {
                throw new Vps_Exception("This would return all pages. You don't want this.");
                $pageIds = array_keys($this->_pageData);
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

    public function getChildData($parentData, $constraints)
    {
        $ret = parent::getChildData($parentData, $constraints);
        foreach ($this->getChildIds($parentData, $constraints) as $id) {
            $ret[] = $this->_createData($parentData, $id);
        }
        return $ret;
    }
    protected function _createData($parentData, $id)
    {
        $page = $this->_pageData[$id];
        if (!$parentData || ($parentData instanceof Vps_Component_Data_Root && $page['parent_id'])) {
            if (!$page['parent_id']) {
                $parentData = Vps_Component_Data_Root::getInstance();
            } else {
                $parentData = Vps_Component_Data_Root::getInstance()
                                    ->getComponentById($page['parent_id']);
            }
        }
        return parent::_createData($parentData, $id);
    }

    protected function _formatConfig($parentData, $id)
    {
        $data = array();
        $page = $this->_pageData[$id];
        $data['filename'] = $page['filename'];
        $data['rel'] = '';
        $data['name'] = $page['name'];
        $data['isPage'] = true;
        $data['componentId'] = $page['id'];
        $data['componentClass'] = $this->_getChildComponentClass($page['component']);
        $data['row'] = (object)$page;
        $data['parent'] = $parentData;
        return $data;
    }
    protected function _getIdFromRow($id)
    {
        return $id;
    }
    public function createsPages()
    {
        return true;
    }

    protected function _getDataClass($config, $id)
    {
        if ($id == $this->_pageHome) {
            return 'Vps_Component_Data_Home';
        } else {
            return parent::_getDataClass($config, $id);
        }
    }
}
