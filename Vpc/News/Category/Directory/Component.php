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
            'table' => 'Vps_Dao_Pool'
        );

        //für News-Kategorien Box
        $ret['categoryChildId'] = 'categories';
        $ret['categoryName'] = trlVps('Categories');

        $ret['assetsAdmin']['files'][] = 'vps/Vpc/News/Category/Directory/Plugin.js';
        $ret['assetsAdmin']['dep'][] = 'VpsPool';
        return $ret;
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
