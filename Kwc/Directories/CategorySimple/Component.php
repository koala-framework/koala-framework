<?php
/**
 * Simple Categorization for Directory
 *
 * Does not create subpages like normal Categories but provides a list
 * (Kwc_Directories_CategorySimple_List_Component) (sould be used as a pagetype) which selects a
 * category and show all entries for the selected category.
 *
 * To use you have to extend Kwc_Directories_CategorySimple_CategoriesToItemsModel, set the 'Item'-
 * Reference in the model and set the new model class in the component settings.
 */
class Kwc_Directories_CategorySimple_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['categoryToItemModelName'] = 'Kwc_Directories_CategorySimple_CategoriesToItemsModel';
        $ret['componentName'] = trlKwfStatic('Categories');
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Directories/CategorySimple/Plugin.js';
        $ret['hasModifyItemData'] = true;
        return $ret;
    }

    public static function modifyItemData(Kwf_Component_Data $item, $componentClass)
    {
        $model = Kwf_Model_Abstract::getInstance(
            Kwc_Abstract::getSetting($componentClass, 'categoryToItemModelName')
        );
        $select = $model->select()
            ->whereEquals('item_id', $item->row->id);
        $categories = array();
        foreach ($model->getRows($select) as $category) {
            $categories[] = $category->getParentRow('Category')->name;
        }
        asort($categories);
        $item->categories = $categories;
    }
}
