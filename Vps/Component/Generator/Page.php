<?php
class Vps_Component_Generator_Page extends Vps_Component_Generator_Abstract
    implements Vps_Component_Generator_Page_Interface, Vps_Component_Generator_PseudoPage_Interface
{
    protected $_componentClass = 'row';
    protected $_idSeparator = false;
    protected $_loadTableFromComponent = false;

    protected $_pageData;
    protected $_pageParent;
    protected $_pageFilename;
    protected $_pageComponentParent;
    protected $_pageComponent;
    protected $_pageHome;
    protected $_pageCategory;
    protected $_pageDomain;
    protected $_pageDomainFilename;

    protected function _preparePageData($parentData, $s)
    {
        if ($this->_pageData) return;

        $this->_pageData = array();
        $this->_pageParent = array();
        $this->_pageFilename = array();
        $this->_pageComponentParent = array();
        $this->_pageComponent = array();
        $this->_pageHome = array();
        if (isset($this->_settings['model'])) {
            $select = $this->_getModel()->select()->order('pos');
            $rows = $this->_getModel()->fetchAll($select)->toArray();
        } else {
            $select = new Zend_Db_Select(Vps_Registry::get('db'));
            $select->from('vps_pages', array('id', 'parent_id', 'component', 'visible',
                                        'filename', 'hide', 'category', 'domain', 'name', 'is_home', 'tags'));
            $select->order('pos');
            $domains = $this->getDomains();
            if ($domains) $select->where("domain IN ('" . implode("', '", $domains) . "')", '');
            $rows = $select->query()->fetchAll();
        }
        foreach ($rows as $row) {
            $this->_pageData[$row['id']] = $row;
            $parentId = $row['parent_id'];
            if (is_null($parentId)) $parentId = 0;
            $domain = isset($row['domain']) ? $row['domain'] : '';
            $this->_pageChilds[$parentId][] = $row['id'];
            $this->_pageFilename[$parentId][$row['filename']] = $row['id'];
            if ($parentId == 0)
                $this->_pageDomainFilename[$domain][$row['filename']] = $row['id'];
            $this->_pageComponentParent[$parentId][$row['component']][] = $row['id'];
            $this->_pageComponent[$row['component']][] = $row['id'];
            $this->_pageCategory[$row['category']][] = $row['id'];
            $this->_pageDomain[$domain][] = $row['id'];
            if ($row['is_home']) $this->_pageHome[$domain] = $row['id'];
        }
    }

    public function getDomains() {
        return null;
    }

    protected function _getInitWhere()
    {
        return array();
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
        $this->_preparePageData($parentData, $select);
        $select = $this->_formatSelect($parentData, $select);
        if (is_null($select)) return array();
        $pageIds = $this->_getPageIds($parentData, $select);

        $ret = array();
        foreach ($pageIds as $pageId) {
            $page = $this->_pageData[$pageId];
            if ($select->hasPart(Vps_Component_Select::WHERE_SHOW_IN_MENU)) {
                $menu = $select->getPart(Vps_Component_Select::WHERE_SHOW_IN_MENU);
                if ($menu == $page['hide']) continue;
            }
            if ($select->hasPart(Vps_Component_Select::WHERE_SUBROOT)) {
                $subroot = $select->getPart(Vps_Component_Select::WHERE_SUBROOT);
                $domain = $subroot[count($subroot)-1]->row->id;
                if ($domain != $page['domain']) continue;
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

    protected function _getPageIds($parentData, $select)
    {
        if ($parentData) {
            if ($parentData->componentClass == $this->_class) {
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
            $pageId = $this->_getPageIdHome($parentData);
            if ($pageId) $pageIds[] = $pageId;
        } else if (isset($parentId)) {
            if ($select->hasPart(Vps_Component_Select::WHERE_FILENAME)) {
                $filename = $select->getPart(Vps_Component_Select::WHERE_FILENAME);
                $pageId = $this->_getPageIdByFilename($parentData, $filename);
                if ($pageId) $pageIds[] = $pageId;
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
        return $pageIds;
    }

    protected function _getPageIdByFilename($parentData, $filename)
    {
        $parentId = $parentData->dbId;
        if ($parentData->componentClass == $this->_class) $parentId = 0;
        if (isset($this->_pageFilename[$parentId][$filename])) {
            return $this->_pageFilename[$parentId][$filename];
        }
        return null;
    }

    protected function _getPageIdHome($parentData)
    {
        if (isset($this->_pageHome[''])) {
            return $this->_pageHome[''];
        }
        return null;
    }

    protected function _createData($parentData, $id, $select)
    {
        $page = $this->_pageData[$id];

        if (!$parentData || ($parentData->componentClass == $this->_class && $page['parent_id'])) {
            if (!$page['parent_id']) {
                $parentData = $this->_getParentDataByRow($page);
            } else {
                $c = array();
                if ($select->hasPart(Vps_Component_Select::IGNORE_VISIBLE)) {
                    $c['ignoreVisible'] = $select->getPart(Vps_Component_Select::IGNORE_VISIBLE);
                }
                $parentData = Vps_Component_Data_Root::getInstance()
                                    ->getComponentById($page['parent_id'], $c);
            }
        }
        if (!$parentData) return null;
        //if ($parentData->componentClass != $this->_class) return null;
        return parent::_createData($parentData, $id, $select);
    }

    protected function _getParentDataByRow($row)
    {
        return Vps_Component_Data_Root::getInstance();
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
        if ($this->_pageData[$id]['is_home']) {
            return 'Vps_Component_Data_Home';
        } else {
            return parent::_getDataClass($config, $id);
        }
    }
}
