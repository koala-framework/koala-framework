<?php
class Vps_Component_Generator_Page extends Vps_Component_Generator_Abstract
    implements Vps_Component_Generator_Page_Interface, Vps_Component_Generator_PseudoPage_Interface
{
    protected $_componentClass = 'row';
    protected $_idSeparator = false;
    protected $_loadTableFromComponent = false;
    private $_pageData;
    private $_pageParent;
    private $_pageFilename;
    private $_pageComponentParent;
    private $_pageComponent;
    private $_pageHome;
    private $_pageCategory;

    protected function _init()
    {
        parent::_init();

        $this->_pageData = array();
        $this->_pageParent = array();
        $this->_pageFilename = array();
        $this->_pageComponentParent = array();
        $this->_pageComponent = array();
        $this->_pageHome = 0;
        if (isset($this->_settings['model'])) {
            $rows = $this->_getModel()->fetchAll(null, 'pos')->toArray();
        } else {
            $select = new Zend_Db_Select(Vps_Registry::get('db'));
            $select->from('vps_pages', array('id', 'parent_id', 'component', 'visible',
                                        'filename', 'hide', 'category', 'name', 'is_home', 'tags'));
            $select->order('pos');
            $rows = $select->query()->fetchAll();
        }
        foreach ($rows as $row) {
            $this->_pageData[$row['id']] = $row;
            $parentId = $row['parent_id'];
            if (is_null($parentId)) $parentId = 0;
            $this->_pageChilds[$parentId][] = $row['id'];
            $this->_pageFilename[$parentId][$row['filename']] = $row['id'];
            $this->_pageComponentParent[$parentId][$row['component']][] = $row['id'];
            $this->_pageComponent[$row['component']][] = $row['id'];
            $this->_pageCategory[$row['category']][] = $row['id'];
            if ($row['is_home']) $this->_pageHome = $row['id'];
        }
    }

    protected function _formatSelectFilename(Vps_Component_Select $select)
    {
        return $select;
    }

    protected function _formatSelectHome(Vps_Component_Select $select)
    {
        return $select;
    }

    public function getChildData($parentData, $select = array())
    {
        $select = $this->_formatSelect($parentData, $select);
        if (is_null($select)) return array();
        if ($parentData) {
            if ($parentData instanceof Vps_Component_Data_Root ||
                is_instance_of($parentData->componentClass, 'Vpc_Root_Category_Component')
            ) {
                $parentId = 0;
            } else {
                $parentId = $parentData->dbId;
            }
        }
        $pageIds = array();
        if ($id = $select->getPart(Vps_Component_Select::WHERE_ID)) {
            if (isset($this->_pageData[$id])) {
                if ($select->hasPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES)) {
                    $selectClasses = $select->getPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES);
                    $class = $this->_settings['component'][$this->_pageData[$id]['component']];
                    if (in_array($class, $selectClasses)) {
                        $pageIds[] = $id;
                    }
                } else {
                    $pageIds[] = $id;
                }
            }
        } else if ($select->getPart(Vps_Component_Select::WHERE_HOME)) {
            if ($this->_pageHome) {
                $pageIds[] = $this->_pageHome;
            }
        } else if (isset($parentId)) {
            if ($select->hasPart(Vps_Component_Select::WHERE_FILENAME)) {
                $filename = $select->getPart(Vps_Component_Select::WHERE_FILENAME);
                if (isset($this->_pageFilename[$parentId][$filename])) {
                    $pageIds[] = $this->_pageFilename[$parentId][$filename];
                }
            } else if ($select->hasPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES)) {
                $selectClasses = $select->getPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES);
                $keys = array();
                foreach ($selectClasses as $selectClass) {
                    $key = array_search($selectClass, $this->_settings['component']);
                    if ($key) $keys[] = $key;
                }
                foreach ($keys as $key) {
                    if (isset($parentId) && isset($this->_pageComponentParent[$parentId][$key])) {
                        $pageIds = array_merge($pageIds, $this->_pageComponentParent[$parentId][$key]);
                    }
                    if (!isset($parentId) && isset($this->_pageComponent[$key])) {
                        $pageIds = array_merge($pageIds, $this->_pageComponent[$key]);
                    }
                }
            } else {
                if (isset($this->_pageChilds[$parentId])) {
                    $pageIds = $this->_pageChilds[$parentId];
                }
            }
        } else if ($select->hasPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES)) {
            $selectClasses = $select->getPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES);
            $keys = array();
            foreach ($selectClasses as $selectClass) {
                $key = array_search($selectClass, $this->_settings['component']);
                if ($key) $keys[] = $key;
            }
            foreach ($keys as $key) {
                if (isset($this->_pageComponent[$key])) {
                    $pageIds = array_merge($pageIds, $this->_pageComponent[$key]);
                }
            }
        } else {
            throw new Vps_Exception("This would return all pages. You don't want this.");
        }
        if ($parentData && is_instance_of($parentData->componentClass, 'Vpc_Root_Category_Component')) {
            $pageIds = array_intersect($this->_pageCategory[$parentData->row->id], $pageIds);
        }

        $ret = array();
        foreach ($pageIds as $pageId) {
            $page = $this->_pageData[$pageId];
            if ($select->hasPart(Vps_Component_Select::WHERE_SHOW_IN_MENU)) {
                $menu = $select->getPart(Vps_Component_Select::WHERE_SHOW_IN_MENU);
                if ($menu == $page['hide']) continue;
            }
            static $showInvisible;
            if (is_null($showInvisible)) {
                $showInvisible = Vps_Registry::get('config')->showInvisible;
            }
            if ($select->getPart(Vps_Component_Select::IGNORE_VISIBLE)) {
            } else if (!$showInvisible) {
                if (!$this->_pageData[$pageId]['visible']) continue;
            }

            $d = $this->_createData($parentData, $pageId, $select);
            if ($d) $ret[] = $d;

            if ($select->hasPart(Vps_Model_Select::LIMIT_COUNT)) {
                if (count($ret) >= $select->getPart(Vps_Model_Select::LIMIT_COUNT)) break;
            }
        }
        return $ret;
    }

    protected function _createData($parentData, $id, $select)
    {
        $page = $this->_pageData[$id];
        if (!$parentData || (($parentData instanceof Vps_Component_Data_Root) && $page['parent_id'])) {
            if (!$page['parent_id']) {
                $root = Vps_Component_Data_Root::getInstance();
                if ($root->componentClass == $this->_class) {
                    $parentData = $root;
                } else {
                    $parentData = $root->getChildComponent('-' . $page['category']);
                }
            } else {
                $c = array();
                if ($select->hasPart(Vps_Component_Select::IGNORE_VISIBLE)) {
                    $c['ignoreVisible'] = $select->getPart(Vps_Component_Select::IGNORE_VISIBLE);
                }
                $parentData = Vps_Component_Data_Root::getInstance()
                                    ->getComponentById($page['parent_id'], $c);
                if (!$parentData) {
                    return null;
                }
            }
        }
        return parent::_createData($parentData, $id, $select);
    }

    protected function _formatConfig($parentData, $id)
    {
        $data = array();
        $page = $this->_pageData[$id];
        $data['filename'] = $page['filename'];
        $data['rel'] = '';
        $data['name'] = $page['name'];
        $data['isPage'] = true;
        $data['inherits'] = true;
        $data['isPseudoPage'] = true;
        $data['componentId'] = $page['id'];
        $data['componentClass'] = $this->_getChildComponentClass($page['component']);
        $data['row'] = (object)$page;
        $data['parent'] = $parentData;
        $data['isHome'] = $page['is_home'];
        $data['visible'] = $page['visible'];
        if (isset($page['tags']) && $page['tags']) {
            $data['tags'] = explode(',', $page['tags']);
        } else {
            $data['tags'] = array();
        }
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
