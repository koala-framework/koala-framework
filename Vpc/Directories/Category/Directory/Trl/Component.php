<?php
class Vpc_Directories_Category_Directory_Trl_Component extends Vpc_Directories_Item_Directory_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['hasModifyItemData'] = true;
        $ret['childModel'] = 'Vpc_Directories_Category_Directory_Trl_CategoriesModel';
        return $ret;
    }

    public static function modifyItemData(Vps_Component_Data $item, $componentClass)
    {
        $model = Vpc_Abstract::getSetting(Vpc_Abstract::getSetting($componentClass, 'masterComponentClass'), 'categoryToItemModelName');
        $model = Vps_Model_Abstract::getInstance($model);
        $itemRef = $model->getReference('Item');
        $catRef = $model->getReference('Category');
        $rows = $model->getRows($model->select()->whereEquals($itemRef['column'], $item->chained->row->id));
        $item->categories = array();
        foreach ($rows as $row) {
            $item->categories[] = $item->parent->getChildComponent(array('componentClass'=>$componentClass))
                ->getChildComponent('_'.$row->{$catRef['column']});
        }
    }
}
