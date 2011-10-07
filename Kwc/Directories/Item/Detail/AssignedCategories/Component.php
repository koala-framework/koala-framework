<?php
class Vpc_Directories_Item_Detail_AssignedCategories_Component
    extends Vpc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] =
            'Vpc_Directories_Item_Detail_AssignedCategories_View_Component';
        $ret['useDirectorySelect'] = false;
        return $ret;
    }

    protected function _getItemDirectory()
    {
        return $this->_getItemDetail()->parent->getChildComponent('_category');
    }

    protected function _getItemDetail()
    {
        return $this->getData()->parent;
    }

    public function getItemDetail()
    {
        return $this->_getItemDetail();
    }

    // TODO Cache
    /*
    public function getCacheVars()
    {
        return $this->getData()->getChildComponent('-view')->getComponent()->getCacheVars();
    }
    */

    public function getSelect()
    {
        $ret = parent::getSelect();
        $categoryDirectory = $this->getItemDirectory()->getComponent();

        $refData = Vpc_Directories_Category_Detail_List_Component::getTableReferenceData(
            Vpc_Abstract::getSetting(get_class($categoryDirectory), 'categoryToItemModelName'),
            'Category'
        );

        $ret->join($refData['tableName'],
            "{$refData['refTableName']}.{$refData['refItemColumn']} = "
                ."{$refData['tableName']}.{$refData['itemColumn']}", array()
        );

        $refDataItem = Vpc_Directories_Category_Detail_List_Component::getTableReferenceData(
            Vpc_Abstract::getSetting(get_class($categoryDirectory), 'categoryToItemModelName'),
            'Item'
        );

        $ret->where(
            $refDataItem['tableName'].'.'.$refDataItem['itemColumn'].' = ?',
            $this->_getItemDetail()->row->id
        );

        return $ret;
    }
}
