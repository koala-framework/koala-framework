<?php
class Vpc_Directories_CategoryTree_View_Component
    extends Vpc_Directories_Category_View_Component
{
    private $_viewComponents = array();

    protected function _getCountCategoryIds($item)
    {
        $ret = $item->row->getRecursiveChildCategoryIds(array(
            'visible = 1'
        ));
        return $ret;
    }

    public function getCacheStaticVars()
    {
        // TODO: Cache löschen komplett neu
        $ret[] = array(
            'model' => 'Vpc_Directories_CategoryTree_Directory_ItemsToCategoriesModel',
            'id' => null,
            'callback' => true
        );
        $ret[] = array(
            'model' => 'Vpc_Directories_CategoryTree_Directory_Model',
            'id' => null,
            'callback' => true
        );
        return $ret;
    }

    public function onCacheCallback($row)
    {
        if ($row instanceof Vps_Db_Table_Row && $row->getTable() instanceof Vpc_Directories_CategoryTree_Directory_ItemsToCategoriesModel) {
            $info = Vpc_Directories_Category_Detail_List_Component::getTableReferenceData(
                get_class($row->getTable()), $schema = 'Category'
            );
            $table = new $info['refTableName']();

            $parentRow = $table->find($row->category_id)->current();

            do {
                foreach ($this->_getRemoveCacheViewComponents() as $c) {
                    $cacheId = $c->getItemCountCacheId($parentRow);
                    Vpc_Directories_CategoryTree_View_Component::getItemCountCache()->remove($cacheId);
                }

                $parentRow = $parentRow->findParentRow($info['refTableName']);
            } while ($parentRow);
        } else if ($row instanceof Vps_Db_Table_Row && $row->getTable() instanceof Vpc_Directories_CategoryTree_Directory_Model) {
            $parentRow = $row;
            do {
                foreach ($this->_getRemoveCacheViewComponents() as $c) {
                    $cacheId = $c->getItemCountCacheId($parentRow);
                    Vpc_Directories_CategoryTree_View_Component::getItemCountCache()->remove($cacheId);
                }

                $parentRow = $parentRow->findParentRow($row->getTable()->info(Zend_Db_Table_Abstract::NAME));
            } while ($parentRow);
        } else {
            // Todo: wenn item in der admin bearbeitet wird (zB visible auf 0),
            // dann müsste man es neu berechnen. wird atm durch die cacheLifetime kompensiert
        }
    }

    protected function _getRemoveCacheViewComponents()
    {
        if (!$this->_viewComponents) {
            $components = Vps_Component_Data_Root::getInstance()->getComponentsByClass(
                'Vpc_Directories_Category_View_Component'
            );
            foreach ($components as $c) {
                $this->_viewComponents[] = $c->getComponent();
            }
        }
        return $this->_viewComponents;
    }
}

