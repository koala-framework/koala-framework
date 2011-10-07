<?php
class Vpc_Directories_Category_Directory_Component extends Vpc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Vpc_Directories_Category_Directory_CategoriesModel';
        $ret['generators']['detail']['class'] = 'Vpc_Directories_Category_Directory_Generator';
        $ret['generators']['detail']['component'] = 'Vpc_Directories_Category_Detail_Component';
        $ret['generators']['detail']['showInMenu'] = true;
        $ret['generators']['detail']['nameColumn'] = 'name';

        // zB für Kategorien Box
        $ret['categoryName'] = trlVps('Categories');

        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Directories/Category/Directory/Plugin.js';

        $ret['hasModifyItemData'] = true;
        $ret['categoryToItemModelName'] = null;

        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);
        if (isset($settings['pool'])) {
            throw new Vps_Exception("Directories_Category doesn't use pools anymore");
        }
    }

    public static function modifyItemData(Vps_Component_Data $item, $componentClass)
    {
        if (!Vpc_Abstract::hasSetting($componentClass, 'categoryToItemModelName')
            && Vpc_Abstract::hasSetting($componentClass, 'categoryToItemTableName')
        ) {
            // setting has changed
            throw new Vps_Exception("Setting 'categoryToItemTableName' has been renamed to 'categoryToItemModelName' and must be a Vps_Model");
        }

        if (!$model = Vpc_Abstract::getSetting($componentClass, 'categoryToItemModelName')) {
            throw new Vps_Exception("Setting 'categoryToItemModelName' must be set in component '$componentClass'");
        }

        // getting the intersection rows
        $model = Vps_Model_Abstract::getInstance($model);
        $itemRef = $model->getReference('Item');
        $catRef = $model->getReference('Category');
        $rows = $model->getRows($model->select()->whereEquals($itemRef['column'], $item->row->id));
        $item->categories = array();
        foreach ($rows as $row) {
            $item->categories[] = $item->parent->getChildComponent(array('componentClass'=>$componentClass))
                ->getChildComponent('_'.$row->{$catRef['column']});
        }
    }

    public function getSelect()
    {
        return $this->getData()->getGenerator('detail')->select($this->getData());
    }
}
