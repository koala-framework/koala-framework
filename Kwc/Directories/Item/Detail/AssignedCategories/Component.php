<?php
class Kwc_Directories_Item_Detail_AssignedCategories_Component
    extends Kwc_Directories_List_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['view'] =
            'Kwc_Directories_Item_Detail_AssignedCategories_View_Component';
        $ret['useDirectorySelect'] = false;
        return $ret;
    }

    public static function getItemDirectoryClasses($directoryClass)
    {
        $class = self::_getParentItemDirectoryClasses($directoryClass, 1);
        return array(Kwc_Abstract::getChildComponentClass($class, 'category'));
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

        $refData = Kwc_Directories_Category_Detail_List_Component::getTableReferenceData(
            Kwc_Abstract::getSetting(get_class($categoryDirectory), 'categoryToItemModelName'),
            'Category'
        );

        $ret->join($refData['tableName'],
            "{$refData['refTableName']}.{$refData['refItemColumn']} = "
                ."{$refData['tableName']}.{$refData['itemColumn']}", array()
        );

        $refDataItem = Kwc_Directories_Category_Detail_List_Component::getTableReferenceData(
            Kwc_Abstract::getSetting(get_class($categoryDirectory), 'categoryToItemModelName'),
            'Item'
        );

        $ret->where(
            $refDataItem['tableName'].'.'.$refDataItem['itemColumn'].' = ?',
            $this->_getItemDetail()->row->id
        );

        return $ret;
    }
}
