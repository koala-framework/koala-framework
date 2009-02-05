<?php
class Vpc_Directories_Category_View_Component
    extends Vpc_Directories_List_ViewPage_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['linkPrefix'] = '';
        return $ret;
    }

    public function getPartialVars($partial, $nr, $info)
    {
        $ret = parent::getPartialVars($partial, $nr, $info);
        $ret['placeholder'] = $this->_getSetting('placeholder');
        return $ret;
    }

    public function getItemCountCacheId($row)
    {
        // Row kann von hier (Model) oder von Admin (DB-Row) kommen
        $c = $this->getData();
        while ($c) {
            $isSubroot = Vps_Component_Abstract::getFlag($c->componentClass, 'subroot');
            if ($isSubroot) {
                $highestSubRoot = $c;
            }
            $c = $c->parent;
        }
        if (!$highestSubRoot) {
            $cacheClassId = '';
        } else {
            $cacheClassId = $highestSubRoot->componentId;
        }

        if ($row instanceof Vps_Model_Row_Interface) $row = $row->getRow();
        return preg_replace('/[^a-zA-Z0-9_]/', '_', $cacheClassId).'VpcDirectoriesCategoryTreeViewComponent_category'.get_class($row->getTable()).$row->id.'_itemCount';
    }

    public static function getItemCountCache()
    {
        $frontendOptions = array('lifetime' => 3600);
        $backendOptions = array('cache_dir' => 'application/cache/component/');
        return Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
    }

    protected function _getCountCategoryIds($item)
    {
        return array($item->row->id);
    }

    protected function _getItems($select = null)
    {
        $items = parent::_getItems($select);

        $cache = self::getItemCountCache();

        foreach ($items as &$item) {
            $cacheId = $this->getItemCountCacheId($item->row);

            if (($item->listCount = $cache->load($cacheId)) == false) {
                if (!isset($itemDirectory)) {
                    $itemDirectory = $this->getData()->parent->getComponent()->getItemDirectory();
                }
                if (!isset($connectData)) {
                    $tableName = Vpc_Abstract::getSetting(
                        $itemDirectory->componentClass, 'categoryToItemTableName'
                    );
                    $connectData = Vpc_Directories_Category_Detail_List_Component::getTableReferenceData(
                        $tableName
                    );
                }
                if (!isset($directoryComponent)) {
                    $directoryComponent = $this->getData()->parent->getComponent()
                        ->getItemDirectory()->parent->getComponent();
                }

                $categoryIds = $this->_getCountCategoryIds($item);

                $select = $directoryComponent->getSelect();
                if (!Vpc_Abstract::getSetting(get_class($directoryComponent), 'generatorJoins')) {
                    $select->join(
                        $connectData['tableName'],
                        "$connectData[refTableName].$connectData[refItemColumn] = $connectData[tableName].$connectData[itemColumn]",
                        array()
                    );
                }
                $select->where("$connectData[tableName].category_id IN(".implode(',', $categoryIds).")");

                $item->listCount = $directoryComponent->getData()->countChildComponents($select);

                $cache->save($item->listCount, $cacheId);
            }
        }

        return $items;
    }
}

