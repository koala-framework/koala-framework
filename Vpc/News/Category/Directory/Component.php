<?php
class Vpc_News_Category_Directory_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['pool'] = 'Newskategorien'; //todo zu ph, hier engl.
        $ret['generators']['detail'] = array(
            'class' => 'Vpc_News_Category_Directory_Generator',
            'component' => 'Vpc_News_Category_Detail_Component',
            'table' => 'Vps_Dao_Pool',
            'showInMenu' => true
        );

        //für News-Kategorien Box
        $ret['categoryChildId'] = 'categories';
        $ret['categoryName'] = trlVps('Categories');

        $ret['assetsAdmin']['files'][] = 'vps/Vpc/News/Category/Directory/Plugin.js';
        $ret['assetsAdmin']['dep'][] = 'VpsPool';

        $ret['hasModifyNewsData'] = true;
        return $ret;
    }

    public static function modifyNewsData(Vps_Component_Data $new)
    {
        $categories = $new->row->findManyToManyRowset('Vps_Dao_Pool',
                    'Vpc_News_Category_Directory_NewsToCategoriesModel');
        $new->categories = array();
        foreach ($categories as $c) {
            $new->categories[] = Vps_Component_Data_Root::getInstance()
                ->getComponentById($new->parent->componentId.'_categories_'.$c->id);
        }
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $childComponents = $this->getData()->getChildComponents(array('generator' => 'detail'));

        $ret['categories'] = $childComponents;
        return $ret;
    }
    public function getNewsComponent()
    {
        return $this->getData()->parent;
    }
}
