<?php
class Kwc_Directories_Category_Directory_Trl_Component extends Kwc_Directories_Item_Directory_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['hasModifyItemData'] = true;
        $ret['childModel'] = 'Kwc_Directories_Category_Directory_Trl_CategoriesModel';
        return $ret;
    }

    public static function modifyItemData(Kwf_Component_Data $item, $componentClass)
    {
        $model = Kwc_Abstract::getSetting(Kwc_Abstract::getSetting($componentClass, 'masterComponentClass'), 'categoryToItemModelName');
        $model = Kwf_Model_Abstract::getInstance($model);
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
