<?php
class Vpc_Directories_Item_Detail_AssignedCategories_Component
    extends Vpc_Directories_List_Component
{
    protected function _getItemDirectory()
    {
        return $this->getData()->parent->parent->getChildComponent('_category');
    }

    public function getSelect()
    {
        $ret = parent::getSelect();
        $categoryDirectory = $this->getItemDirectory()->getComponent();

        $refData = Vpc_Directories_Category_Detail_List_Component::getTableReferenceData(
            Vpc_Abstract::getSetting(get_class($categoryDirectory), 'categoryToItemTableName'),
            'Category'
        );

        $ret->join($refData['tableName'],
            "{$refData['refTableName']}.{$refData['refItemColumn']} = "
                ."{$refData['tableName']}.{$refData['itemColumn']}", array()
        );

        $refDataItem = Vpc_Directories_Category_Detail_List_Component::getTableReferenceData(
            Vpc_Abstract::getSetting(get_class($categoryDirectory), 'categoryToItemTableName')
        );

        $ret->where(
            $refDataItem['tableName'].'.'.$refDataItem['itemColumn'].' = ?',
            $this->getData()->parent->row->id
        );

        return $ret;
    }
}
