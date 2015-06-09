<?php
class Kwc_Directories_Category_Directory_Component extends Kwc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Kwc_Directories_Category_Directory_CategoriesModel';
        $ret['generators']['detail']['class'] = 'Kwc_Directories_Category_Directory_Generator';
        $ret['generators']['detail']['component'] = 'Kwc_Directories_Category_Detail_Component';
        $ret['generators']['detail']['showInMenu'] = true;
        $ret['generators']['detail']['nameColumn'] = 'name';

        // zB für Kategorien Box
        $ret['categoryName'] = trlKwfStatic('Categories');

        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Directories/Category/Directory/Plugin.js';

        $ret['hasModifyItemData'] = true;
        $ret['categoryToItemModelName'] = null;

        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);
        if (isset($settings['pool'])) {
            throw new Kwf_Exception("Directories_Category doesn't use pools anymore");
        }
    }

    public static function modifyItemData(Kwf_Component_Data $item, $componentClass)
    {
        if (!Kwc_Abstract::hasSetting($componentClass, 'categoryToItemModelName')
            && Kwc_Abstract::hasSetting($componentClass, 'categoryToItemTableName')
        ) {
            // setting has changed
            throw new Kwf_Exception("Setting 'categoryToItemTableName' has been renamed to 'categoryToItemModelName' and must be a Kwf_Model");
        }

        if (!$model = Kwc_Abstract::getSetting($componentClass, 'categoryToItemModelName')) {
            throw new Kwf_Exception("Setting 'categoryToItemModelName' must be set in component '$componentClass'");
        }

        // getting the intersection rows
        $model = Kwf_Model_Abstract::getInstance($model);
        $itemRef = $model->getReference('Item');
        $catRef = $model->getReference('Category');
        $rows = $model->getRows($model->select()->whereEquals($itemRef['column'], $item->row->id));
        $item->categories = array();
        foreach ($rows as $row) {
            $cat = $item->parent->getChildComponent(array('componentClass'=>$componentClass))
                ->getChildComponent('_'.$row->{$catRef['column']});
            if ($cat) {
                $item->categories[] = $cat;
            }
        }
    }

    public function getSelect()
    {
        return $this->getData()->getGenerator('detail')->select($this->getData());
    }
}
