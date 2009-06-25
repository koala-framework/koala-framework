<?php
abstract class Vpc_Directories_Category_ShowCategories_Abstract_Component extends Vpc_Directories_List_Component
{
    abstract public function getCategoryIds();

    public function getSelect()
    {
        $select = parent::getSelect();
        if (!$select) return null;

        $tableName = Vpc_Abstract::getSetting(
            $this->getItemDirectory()->getChildComponent('_categories')->componentClass,
            'categoryToItemModelName'
        );
        $refData = Vpc_Directories_Category_Detail_List_Component::getTableReferenceData($tableName);

        $select->join($refData['tableName'],
                      $refData['tableName'].'.'.$refData['itemColumn'].'='
                        .$refData['refTableName'].'.'.$refData['refItemColumn'],
                      array());
        $ids = $this->getCategoryIds();
        if (!$ids) return null;
        $select->where($refData['tableName'].'.category_id IN ('.implode(',', $ids).')');
        return $select;
    }
}
